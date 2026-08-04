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
                    if ($row->status == 1) {
                        return '<span class="badge bg-label-success m-1">Sudah Kirim WA Blast</span>';
                    } else {
                        $log = WhatsappMessagesLog::where('rencana_kerja_id', $row->id)->latest()->first();
                        $reason = $log ? '<br><small class="text-danger">Gagal: ' . $log->failure_reason . '</small>' : '';
                        return '<span class="badge bg-label-danger m-1">Belum Kirim WA Blast</span>' . $reason;
                    }
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

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
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

            // Status 2 = antri (queued), akan jadi 1 ketika semua job selesai
            $model->status = 2;
            $model->save();

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

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
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

            $model->status = 2;
            $model->save();

            return response()->json(['success' => true, 'message' => 'Pesan WA sedang diantri untuk dikirim secara bertahap.']);
        } catch (\Exception $e) {
            Log::error("Failed to queue WhatsApp message with category: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal antri WA: ' . $e->getMessage()], 500);
        }
    }

    public function kirimWaSekolah($id, $id_user)
    {
        try {
            $model = RencanaKerjaT::with('pengawasnama')->findOrFail($id);

            if ($model->is_mandiri == 1) {
                $pengawas = User::with('profile')->find($model->id_pengawas);
                $no_telp = $pengawas->no_telp;
                if (empty($no_telp) && $pengawas->profile) {
                    $no_telp = $pengawas->profile->no_telp;
                }

                if (empty($no_telp)) {
                    return response()->json(['success' => false, 'message' => 'Pengawas belum mengisi nomor HP.'], 400);
                }

                $this->buildMandiriUmpanBalik($model, $pengawas->name, $pengawas->id, $no_telp, $model->id_umpanbalik_category);
            } else {
                $kepalaSekolah = GuruM::findOrFail($id_user);
                $this->buildUmpanBalik($model, $kepalaSekolah->sekolah->nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp);
            }

            $model->status = 2;
            $model->save();

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

    public function buildMandiriUmpanBalik($model, $nama_pengawas, $id_pengawas, $no_telp, $id_category)
    {
        $checkUmpanBalik = UmpanbalikT::where('id_user_pengawas', $id_pengawas)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->where('id_category', $id_category)
            ->first();

        $generate_url = (string) \Illuminate\Support\Str::uuid();
        if (!$checkUmpanBalik) {
            UmpanbalikT::create([
                'id_user' => 0,
                'id_user_pengawas' => $id_pengawas,
                'id_pelaporan' => $model->id,
                'generate_url' => $generate_url,
                'id_pengawas' => $model->id_pengawas,
                'id_category' => $id_category,
                'id_created_by' => Auth::user()->id,
                'id_updated_by' => Auth::user()->id,
                'tgl_rtl' => date('Y-m-d'),
            ]);
            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $generate_url]);
        } else {
            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $checkUmpanBalik->generate_url]);
        }

        // Spintax: variasi salam agar tidak identik
        $pesan = "{Halo|Selamat pagi|Hai} {Pak/Bu|Bapak/Ibu} {$nama_pengawas},\n"
            . "Anda telah membuat Rencana Kerja Mandiri: {$model->nama_program_kerja}.\n"
            . "{Silakan|Mohon} isi umpan balik/refleksi mandiri pada link berikut: {$fullUrl}\n\n"
            . "{Terima kasih|Terimakasih} {atas perhatiannya|}\n"
            . "DelmanSuper Platform";

        $this->dispatchWaJob($no_telp, $pesan, $model, null);
    }

    public function buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp)
    {
        $uniqueUrl = (string) Str::uuid();

        $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->first();

        if ($checkUmpanBalik) {
            $umpanBalik = $checkUmpanBalik;
            $umpanBalik->id_updated_by = Auth::user()->id;
            $umpanBalik->save();
            $fullUrl = url('umpan-balik/' . $umpanBalik->generate_url);
        } else {
            $umpanBalik = new UmpanbalikT();
            $umpanBalik->generate_url = $uniqueUrl;
            $umpanBalik->id_updated_by = Auth::user()->id;
            $umpanBalik->id_pelaporan = $model->id;
            $umpanBalik->id_user = $nama_kepala_sekolah_id;
            $umpanBalik->id_pengawas = $model->id_pengawas;
            $umpanBalik->id_created_by = Auth::user()->id;
            $umpanBalik->tgl_rtl = date('Y-m-d');
            $umpanBalik->id_category = 0;
            $umpanBalik->save();
            $fullUrl = url('umpan-balik/' . $uniqueUrl);
        }

        // Spintax: variasi salam & penutup
        $pesan = "{Yth.|Yang terhormat} {Bapak/Ibu|Bapak atau Ibu} {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran}\n"
            . "pengawas {$model->pengawasnama->name}\n"
            . "akan melakukan kegiatan pengawasan {$model->nama_program_kerja}\n"
            . "ke sekolah.\n"
            . "{Mohon dapat|Silakan} mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "{Terima kasih|Terimakasih} {atas perhatian dan kerja samanya|}\n"
            . "Pesan ini digenerate otomatis oleh DelmanSuper";

        $this->dispatchWaJob($no_telp, $pesan, $model, $nama_kepala_sekolah_id);
    }

    public function buildDynamicUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp, $id_category)
    {
        $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->where('id_category', $id_category)
            ->first();

        if ($checkUmpanBalik) {
            $umpanBalik = $checkUmpanBalik;
            $umpanBalik->id_updated_by = Auth::user()->id;
            $umpanBalik->save();
            $fullUrl = url('dynamic-umpanbalik/' . $id_category . '/' . $umpanBalik->generate_url);
        } else {
            $fullUrl = DynamicUmpanbalikController::generateUmpanbalikUrl(
                $nama_kepala_sekolah_id,
                $model->id,
                $model->id_pengawas,
                $id_category
            );
        }

        // Spintax: variasi salam & penutup
        $pesan = "{Yth.|Yang terhormat} {Bapak/Ibu|Bapak atau Ibu} {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran}\n"
            . "pengawas {$model->pengawasnama->name}\n"
            . "akan melakukan kegiatan pengawasan {$model->nama_program_kerja}\n"
            . "ke sekolah.\n"
            . "{Mohon dapat|Silakan} mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "{Terima kasih|Terimakasih} {atas perhatian dan kerja samanya|}\n"
            . "Pesan ini digenerate otomatis oleh DelmanSuper";

        $this->dispatchWaJob($no_telp, $pesan, $model, $nama_kepala_sekolah_id);
    }

    /**
     * Validasi nomor, siapkan pesan, lalu dispatch ke queue (async).
     * Menggantikan sendWhatsAppMessage() yang synchronous.
     */
    protected function dispatchWaJob($phone, $message, $model, $kepalaSekolahId)
    {
        // Validasi nomor telepon Indonesia
        $validation = WaBlastSafetyService::validatePhoneNumber($phone);
        if (!$validation['valid']) {
            Log::warning("[WA Dispatch] Nomor tidak valid ({$phone}): {$validation['reason']}");

            $logEntry = new WhatsappMessagesLog();
            $logEntry->rencana_kerja_id = $model->id;
            $logEntry->kepala_sekolah_id = $kepalaSekolahId;
            $logEntry->phone_number = $phone;
            $logEntry->message = $message;
            $logEntry->is_sent = false;
            $logEntry->failure_reason = "Nomor tidak valid: {$validation['reason']}";
            $logEntry->save();
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
            $kepalaSekolahId
        );

        Log::info("[WA Dispatch] Job diantri untuk {$phone} (rencana_kerja_id: {$model->id})");
    }
}
