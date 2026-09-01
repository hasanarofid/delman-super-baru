<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UmpanbalikT;
use App\GuruM;
use Illuminate\Http\Request;
use App\Models\RencanaKerjaT;
use App\Kabupaten;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Imports\ImportUser;
use App\Exports\ExportUser;
use App\Models\WhatsappMessagesLog;
use App\SekolahM;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DynamicUmpanbalikController;
use App\Services\WaBlastSafetyService;

class RencanaTugasController extends Controller
{
    //index
    public function index()
    {
        $currentMonth = date('n'); // Numeric representation of the current month (1-12)
        $currentYear = date('Y');  // Current year
        $years = range($currentYear - 5, $currentYear + 5);
        $months = [];

        // Array of month names in Indonesian
        $monthNamesIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Generate the current and next 11 months in Indonesian
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }

        $listPengawas = User::where('role', 'pengawas')->get();
        return view(
            'rencanakerja.index',
            compact(
                'listPengawas',
                'months',
                'currentYear',
                'years'
            )
        );
    }

    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            // Base query with eager loading
            $query = RencanaKerjaT::with('kategoriprogram', 'jenisprogram', 'aspekprogram', 'pengawasnama')->latest();

            // Apply filter for 'pengawas'
            if ($request->has('pengawas') && $request->pengawas !== 'all') {
                $query->where('id_pengawas', $request->pengawas);
            }

            // Apply filter for 'bln' (bulan)
            if ($request->has('bln') && $request->bln !== 'all') {
                $query->where('bulan', $request->bln);
            }

            // Apply filter for 'tahun'
            if ($request->has('tahun') && $request->tahun !== 'all') {
                $query->where('tahun_ajaran', $request->tahun);
            }

            // Apply filter for 'status_wa'
            if ($request->has('status_wa') && $request->status_wa !== 'all') {
                if ($request->status_wa === 'belum_kirim') {
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhereNotIn('status', [1, 2]);
                    });
                } elseif ($request->status_wa === 'sedang_antri') {
                    $query->where('status', 2);
                } elseif ($request->status_wa === 'sudah_kirim') {
                    $query->where('status', 1);
                }
            }

            // Return data for DataTables
            return Datatables::of($query->get())
                ->addIndexColumn()
                ->addColumn('pengawas', function ($row) {
                    return $row->pengawasnama->nip . ' - ' . $row->pengawasnama->name;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d M Y h:i:s');
                })
                ->addColumn('nama_kategori', function ($row) {
                    return $row->kategoriprogram->nama;
                })
                ->addColumn('nama_jenis', function ($row) {
                    return !empty($row->jenisprogram->nama) ? $row->jenisprogram->nama : '-';
                })
                ->addColumn('nama_aspek', function ($row) {
                    return !empty($row->aspekprogram->nama) ? $row->aspekprogram->nama : '-';
                })
                ->addColumn('bulan_tahun', function ($row) {
                    return $row->bulan . ' - ' . $row->tahun_ajaran;
                })
                ->addColumn('nama_sekolah', function ($row) {
                    if ($row->is_mandiri == 1) {
                        return '<span class="badge bg-label-info m-1">Mandiri (Refleksi)</span>';
                    }
                    $sekolahIds = explode(',', $row->sekolah_id);
                    $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();

                    $nama_sekolah = '';
                    foreach ($sekolahs as $sekolah) {
                        $nama_sekolah .= '<span class="badge bg-label-primary m-1" data-sekolah2="' . $sekolah->nama_sekolah . '">' . $sekolah->nama_sekolah . '</span> ';
                    }
                    return $nama_sekolah;
                })
                ->addColumn('status', function ($row) {
                    $log = WhatsappMessagesLog::where('rencana_kerja_id', $row->id)->latest()->first();

                    if ($row->status == 1 || ($log && $log->is_sent)) {
                        return '<span class="badge bg-label-success m-1">Sudah Kirim WA Blast</span>';
                    }

                    $reason = ($log && !empty($log->failure_reason)) ? '<br><small class="text-danger">Gagal: ' . e($log->failure_reason) . '</small>' : '';

                    if ($row->status == 2) {
                        return '<span class="badge bg-label-warning m-1">Sedang Antri WA Blast</span>' . $reason;
                    }

                    return '<span class="badge bg-label-danger m-1">Belum Kirim WA Blast</span>' . $reason;
                })
                ->addColumn('action', function ($row) {
                    $user = Auth::user();
                    if ($user && $user->role == 'Super Admin') {
                        return '<a id="sendWaButton-' . $row->id . '" onclick="kirimWaBlast(' . $row->id . ')" class="btn btn-sm bg-success text-white">
                        <i class="fa fa-envelope"></i> Kirim Wa
                    </a>';
                    } else {
                        return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                    }
                })
                ->rawColumns(['pengawas', 'nama_kategori', 'nama_jenis', 'nama_aspek', 'nama_sekolah', 'status', 'action'])
                ->make(true);
        }

        return view('rencanakerja.index');
    }


    public function kirimWa($id)
    {
        try {
            $model = RencanaKerjaT::with('pengawasnama')->findOrFail($id);
            $id_umpanbalik_category = $model->id_umpanbalik_category;
            $errors = [];

            // Status 2 = antri (queued), akan jadi 1 ketika semua job selesai
            $model->status = 2;
            $model->save();

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
                if (!$pengawas) {
                    return response()->json(['success' => false, 'message' => 'Pengawas tidak ditemukan.'], 400);
                }
                $no_telp = $pengawas->no_telp;
                if (empty($no_telp) && $pengawas->profile) {
                    $no_telp = $pengawas->profile->no_telp;
                }

                if (empty($no_telp)) {
                    return response()->json(['success' => false, 'message' => 'Pengawas belum mengisi nomor HP.'], 400);
                }

                $this->buildMandiriUmpanBalik($model, $pengawas->name, $pengawas->id, $no_telp, $id_umpanbalik_category);
            } else {
                $sekolahIds = explode(',', $model->sekolah_id);
                $sekolahs = SekolahM::with('kepalaSekolahSatu')->whereIn('id', $sekolahIds)->get();

                if ($sekolahs->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'Tidak ada sekolah sasaran.'], 400);
                }

                foreach ($sekolahs as $list) {
                    $nama_sekolah = $list->nama_sekolah;
                    $kepalaSekolah = $list->kepalaSekolahSatu;
                    if ($kepalaSekolah && !empty($kepalaSekolah->no_telp)) {
                        if ($id_umpanbalik_category == 0) {
                            $this->buildUmpanBalik($model, $nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp);
                        } else {
                            $this->buildDynamicUmpanBalik($model, $nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp, $id_umpanbalik_category);
                        }
                    } else {
                        $errors[] = "Kepala sekolah {$nama_sekolah} tidak memiliki nomor telepon.";
                    }
                }
            }

            $message = 'Pesan WA sedang diantri untuk dikirim secara bertahap.';
            if (!empty($errors)) {
                $message .= ' Peringatan: ' . implode(' | ', $errors);
            }

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            Log::error("Failed to queue WhatsApp message: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal kirim WA: ' . $e->getMessage()], 500);
        }
    }

    public function kirimWaWithCategory($id, $id_category)
    {
        try {
            $model = RencanaKerjaT::with('pengawasnama')->findOrFail($id);

            $model->status = 2;
            $model->save();

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
                if (!$pengawas) {
                    return response()->json(['success' => false, 'message' => 'Pengawas tidak ditemukan.'], 400);
                }
                $no_telp = $pengawas->no_telp;
                if (empty($no_telp) && $pengawas->profile) {
                    $no_telp = $pengawas->profile->no_telp;
                }

                if (empty($no_telp)) {
                    return response()->json(['success' => false, 'message' => 'Pengawas belum mengisi nomor HP.'], 400);
                }

                $this->buildMandiriUmpanBalik($model, $pengawas->name, $pengawas->id, $no_telp, $id_category);
            } else {
                $sekolahIds = explode(',', $model->sekolah_id);
                $sekolahs = SekolahM::with('kepalaSekolahSatu')->whereIn('id', $sekolahIds)->get();

                foreach ($sekolahs as $list) {
                    $kepalaSekolah = $list->kepalaSekolahSatu;
                    if ($kepalaSekolah) {
                        if ($id_category == 0) {
                            $this->buildUmpanBalik($model, $list->nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp);
                        } else {
                            $this->buildDynamicUmpanBalik($model, $list->nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp, $id_category);
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Pesan WA sedang diantri untuk dikirim secara bertahap.']);
        } catch (\Exception $e) {
            Log::error("Failed to queue WhatsApp message with category: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal antri WA: ' . $e->getMessage()], 500);
        }
    }

    public function kirimWaSekolah($id, $id_user, $logId = null)
    {
        try {
            $model = RencanaKerjaT::with('pengawasnama')->findOrFail($id);

            $model->status = 2;
            $model->save();

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
                $no_telp = $pengawas ? $pengawas->no_telp : null;
                if (empty($no_telp) && $pengawas && $pengawas->profile) {
                    $no_telp = $pengawas->profile->no_telp;
                }

                if (empty($no_telp)) {
                    return response()->json(['success' => false, 'message' => 'Pengawas belum mengisi nomor HP.'], 400);
                }

                $this->buildMandiriUmpanBalik($model, $pengawas->name, $pengawas->id, $no_telp, $model->id_umpanbalik_category, $logId);
            } else {
                $id_category = $model->id_umpanbalik_category;
                $kepalaSekolah = !empty($id_user) ? GuruM::with('sekolah')->find($id_user) : null;
                if ($kepalaSekolah) {
                    $namaSekolah = $kepalaSekolah->sekolah ? $kepalaSekolah->sekolah->nama_sekolah : 'Sekolah';
                    if ($id_category == 0) {
                        $this->buildUmpanBalik($model, $namaSekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp, $logId);
                    } else {
                        $this->buildDynamicUmpanBalik($model, $namaSekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp, $id_category, $logId);
                    }
                } else {
                    $sekolahIds = explode(',', $model->sekolah_id);
                    $sekolahs = SekolahM::with('kepalaSekolahSatu')->whereIn('id', $sekolahIds)->get();

                    foreach ($sekolahs as $list) {
                        $ks = $list->kepalaSekolahSatu;
                        if ($ks) {
                            if ($id_category == 0) {
                                $this->buildUmpanBalik($model, $list->nama_sekolah, $ks->nama, $ks->id, $ks->no_telp, $logId);
                            } else {
                                $this->buildDynamicUmpanBalik($model, $list->nama_sekolah, $ks->nama, $ks->id, $ks->no_telp, $id_category, $logId);
                            }
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Pesan WA sedang diantri untuk dikirim.']);
        } catch (\Exception $e) {
            Log::error("Failed to queue WhatsApp message: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal kirim WA: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cek status antrian WA blast untuk rencana kerja tertentu.
     */
    public function waStatus($id)
    {
        $model = RencanaKerjaT::findOrFail($id);
        $logs = WhatsappMessagesLog::where('rencana_kerja_id', $id)->get();

        $total   = $logs->count();
        $sent    = $logs->where('is_sent', true)->count();
        $failed  = $logs->where('is_sent', false)->count();
        $pending = DB::table('jobs')->where('queue', 'wa-blast')
            ->where('payload', 'LIKE', "%\"rencanaKerjaId\":{$id}%")
            ->count();

        return response()->json([
            'status'       => $model->status,
            'status_label' => $model->status == 1 ? 'Selesai' : ($model->status == 2 ? 'Sedang Antri' : 'Belum Kirim'),
            'total_log'    => $total,
            'sent'         => $sent,
            'failed'       => $failed,
            'pending_jobs' => $pending,
        ]);
    }

    public function buildMandiriUmpanBalik($model, $nama_pengawas, $id_pengawas, $no_telp, $id_category, $logId = null)
    {
        $checkUmpanBalik = UmpanbalikT::where('id_user_pengawas', $id_pengawas)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->where('id_category', $id_category)
            ->first();

        $generate_url = (string) \Illuminate\Support\Str::uuid();
        if (!$checkUmpanBalik) {
            $checkUmpanBalik = UmpanbalikT::create([
                'id_user' => 0,
                'id_user_pengawas' => $id_pengawas,
                'id_pelaporan' => $model->id,
                'generate_url' => $generate_url,
                'id_pengawas' => $model->id_pengawas,
                'id_category' => $id_category,
                'id_created_by' => Auth::id() ?? $model->id_pengawas ?? 0,
                'id_updated_by' => Auth::id() ?? $model->id_pengawas ?? 0,
                'tgl_rtl' => date('Y-m-d'),
                'jumlah_kirim_wa' => 1,
                'tgl_terakhir_kirim_wa' => now(),
            ]);
            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $generate_url]);
        } else {
            if (\Illuminate\Support\Facades\Schema::hasColumn('umpanbalik_t', 'jumlah_kirim_wa')) {
                $checkUmpanBalik->increment('jumlah_kirim_wa');
                $checkUmpanBalik->tgl_terakhir_kirim_wa = now();
                $checkUmpanBalik->save();
            }
            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $checkUmpanBalik->generate_url]);
        }

        $ref = date('YmdHis') . rand(100, 999);
        $pesan = "Halo Bapak/Ibu *{$nama_pengawas}*, Anda telah membuat Rencana Kerja Mandiri: *{$model->nama_program_kerja}*. Silakan isi umpan balik/refleksi mandiri pada link berikut:*{$fullUrl}* _ref: {$ref}_. Terimakasih";

        $this->dispatchWaJob($no_telp, $pesan, $model, null, $logId);
    }

    public function buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp, $logId = null)
    {
        $uniqueUrl = (string) Str::uuid();

        $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->first();

        $currentUserId = Auth::id() ?? $model->id_pengawas ?? 0;
        if ($checkUmpanBalik) {
            $umpanBalik = $checkUmpanBalik;
            $umpanBalik->id_updated_by = $currentUserId;
            if (\Illuminate\Support\Facades\Schema::hasColumn('umpanbalik_t', 'jumlah_kirim_wa')) {
                $umpanBalik->jumlah_kirim_wa = ($umpanBalik->jumlah_kirim_wa ?? 0) + 1;
                $umpanBalik->tgl_terakhir_kirim_wa = now();
            }
            $umpanBalik->save();
            $fullUrl = url('umpan-balik/' . $umpanBalik->generate_url);
        } else {
            $umpanBalik = new UmpanbalikT();
            $umpanBalik->generate_url = $uniqueUrl;
            $umpanBalik->id_updated_by = $currentUserId;
            $umpanBalik->id_pelaporan = $model->id;
            $umpanBalik->id_user = $nama_kepala_sekolah_id;
            $umpanBalik->id_pengawas = $model->id_pengawas;
            $umpanBalik->id_created_by = $currentUserId;
            $umpanBalik->tgl_rtl = date('Y-m-d');
            $umpanBalik->id_category = 0;
            if (\Illuminate\Support\Facades\Schema::hasColumn('umpanbalik_t', 'jumlah_kirim_wa')) {
                $umpanBalik->jumlah_kirim_wa = 1;
                $umpanBalik->tgl_terakhir_kirim_wa = now();
            }
            $umpanBalik->save();
            $fullUrl = url('umpan-balik/' . $uniqueUrl);
        }

        $nama_pengawas = $model->pengawasnama ? $model->pengawasnama->name : 'Pengawas';
        $pesan = "Yth Bapak / Ibu *{$nama_kepala_sekolah}* Kepala *{$nama_sekolah}*, Pada bulan *{$model->bulan}* *{$model->tahun_ajaran}* pengawas *{$nama_pengawas}* akan melakukan kegiatan pengawasan *{$model->nama_program_kerja}* ke sekolah. Mohon dapat mengisi formulir Monev pada link berikut : *{$fullUrl}* Berikut ini beberapa catatan yang penting: 1. Pastikan link diisi pada hari pengawas melakukan pengawasan. 2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas. Terimakasih";

        $this->dispatchWaJob($no_telp, $pesan, $model, $nama_kepala_sekolah_id, $logId);
    }

    public function buildDynamicUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp, $id_category, $logId = null)
    {
        $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->where('id_category', $id_category)
            ->first();

        $currentUserId = Auth::id() ?? $model->id_pengawas ?? 0;
        if ($checkUmpanBalik) {
            $umpanBalik = $checkUmpanBalik;
            $umpanBalik->id_updated_by = $currentUserId;
            if (\Illuminate\Support\Facades\Schema::hasColumn('umpanbalik_t', 'jumlah_kirim_wa')) {
                $umpanBalik->jumlah_kirim_wa = ($umpanBalik->jumlah_kirim_wa ?? 0) + 1;
                $umpanBalik->tgl_terakhir_kirim_wa = now();
            }
            $umpanBalik->save();
            $fullUrl = url('dynamic-umpanbalik/' . $id_category . '/' . $umpanBalik->generate_url);
        } else {
            $fullUrl = DynamicUmpanbalikController::generateUmpanbalikUrl(
                $nama_kepala_sekolah_id,
                $model->id,
                $model->id_pengawas,
                $id_category
            );
            $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
                ->where('id_pelaporan', $model->id)
                ->where('id_pengawas', $model->id_pengawas)
                ->where('id_category', $id_category)
                ->first();
            if ($checkUmpanBalik && \Illuminate\Support\Facades\Schema::hasColumn('umpanbalik_t', 'jumlah_kirim_wa')) {
                $checkUmpanBalik->jumlah_kirim_wa = 1;
                $checkUmpanBalik->tgl_terakhir_kirim_wa = now();
                $checkUmpanBalik->save();
            }
        }

        $nama_pengawas = $model->pengawasnama ? $model->pengawasnama->name : 'Pengawas';
        $pesan = "Yth Bapak / Ibu *{$nama_kepala_sekolah}* Kepala *{$nama_sekolah}*, Pada bulan *{$model->bulan}* *{$model->tahun_ajaran}* pengawas *{$nama_pengawas}* akan melakukan kegiatan pengawasan *{$model->nama_program_kerja}* ke sekolah. Mohon dapat mengisi formulir Monev pada link berikut : *{$fullUrl}* Berikut ini beberapa catatan yang penting: 1. Pastikan link diisi pada hari pengawas melakukan pengawasan. 2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas. Terimakasih";

        $this->dispatchWaJob($no_telp, $pesan, $model, $nama_kepala_sekolah_id, $logId);
    }

    /**
     * Validasi nomor, siapkan pesan, lalu dispatch ke queue (async).
     * Menggantikan sendWhatsAppMessage() yang synchronous.
     */
    protected function dispatchWaJob($phone, $message, $model, $kepalaSekolahId, $logId = null)
    {
        // Validasi nomor telepon Indonesia
        $validation = WaBlastSafetyService::validatePhoneNumber($phone);
        if (!$validation['valid']) {
            Log::warning("[WA Dispatch] Nomor tidak valid ({$phone}): {$validation['reason']}");

            $logEntry = $logId ? WhatsappMessagesLog::find($logId) : null;
            if (!$logEntry) {
                $logEntry = new WhatsappMessagesLog();
            }
            $logEntry->rencana_kerja_id = $model->id;
            $logEntry->kepala_sekolah_id = $kepalaSekolahId;
            $logEntry->phone_number = $phone;
            $logEntry->message = $message;
            $logEntry->is_sent = false;
            $logEntry->failure_reason = "Nomor tidak valid: {$validation['reason']}";
            try {
                $logEntry->save();
            } catch (\Throwable $e) {
                Log::error("[WA Dispatch] Gagal menyimpan log nomor tidak valid: " . $e->getMessage());
            }
            return;
        }

        $phone = $validation['phone'];

        if (empty(trim($message))) {
            Log::error("[WA Dispatch] Pesan kosong untuk nomor {$phone}");
            return;
        }

        // Branding, spintax, dan anti-spam suffix
        $message = WaBlastSafetyService::prepareMessageBody($message);

        // Dispatch ke queue — tidak blocking HTTP request
        \App\Jobs\SendWhatsappMessageJob::dispatch(
            $phone,
            $message,
            $model->id,
            $kepalaSekolahId,
            $logId
        );

        Log::info("[WA Dispatch] Job diantri untuk {$phone} (rencana_kerja_id: {$model->id})");
    }
}
