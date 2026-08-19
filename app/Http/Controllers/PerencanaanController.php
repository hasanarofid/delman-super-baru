<?php

namespace App\Http\Controllers;

use App\Models\AspekProgram;
use App\Models\JenisProgram;
use App\Models\Kategory;
use App\Models\RencanaKerjaT;
use App\Models\SekolahbinaanT;
use App\Models\TugaskerjaT;
use App\Models\UmpanbalikT;
use App\Models\WhatsappMessagesLog;
use App\SekolahM;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\UmpanbalikCategory;
use App\Models\PengaturanAspekStakeholder;
use Carbon\Carbon;
use App\Services\WaBlastSafetyService;
class PerencanaanController extends Controller
{
    //index
    public function index()
    {
        $kegiatan = TugaskerjaT::with('tugas')
            ->where('id_pengawas', Auth::user()->id)->get();
        $kategory = Kategory::where('type', 'pelaporan')->where('status', 'active')->get();
        $subkategory = [];
        $binaan = SekolahbinaanT::with('sekolah')
            ->where('id_pengawas', Auth::user()->id)->get();
        $currentMonth = date('n'); // Numeric representation of the current month (1-12)
        $currentYear = date('Y');  // Current year
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

        $start = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $monthNumber = $date->month;
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }
        $jenisProgram = JenisProgram::where('status', true)->get();
        $aspekProgram = AspekProgram::where('status', true)->get();

        // Filtering penguncian aspek dari Stakeholder untuk Pengawas
        $user = Auth::user();
        if ($user && $user->role == 'Pengawas') {
            $userKabId = $user->kabupaten_id;
            $userJenjangs = json_decode($user->akses_jenjang, true) ?? [];
            $cMonth = date('n');
            $cYear = date('Y');

            $disabledAspekIds = PengaturanAspekStakeholder::where(function ($q) use ($userKabId) {
                if ($userKabId) {
                    $q->where('kabupaten_id', $userKabId)->orWhereNull('kabupaten_id');
                }
            })->where(function ($q) use ($user) {
                $q->where('pengawas_id', $user->id)->orWhereNull('pengawas_id');
            })->where(function ($q) use ($userJenjangs) {
                if (!empty($userJenjangs) && !in_array('All', $userJenjangs)) {
                    $q->whereIn('jenjang', $userJenjangs)->orWhereNull('jenjang');
                }
            })->where(function ($q) use ($cMonth) {
                $q->where('bulan', $cMonth)->orWhereNull('bulan');
            })->where(function ($q) use ($cYear) {
                $q->where('tahun', $cYear)->orWhereNull('tahun');
            })->where('is_active', 0)->pluck('aspek_program_id')->toArray();

            if (!empty($disabledAspekIds)) {
                $aspekProgram = $aspekProgram->reject(function ($item) use ($disabledAspekIds) {
                    return in_array($item->id, $disabledAspekIds);
                });
            }
        }

        $umpanbalikCategories = UmpanbalikCategory::where('status', true)->get();

        $currentYear = date('Y');
        $years = range($currentYear - 5, $currentYear + 5);
        $months_filter = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view(
            'dashboard_pengawas.perencanaan.index',
            compact(
                'kegiatan'
                ,
                'kategory',
                'subkategory',
                'binaan',
                'months',
                'jenisProgram',
                'aspekProgram',
                'umpanbalikCategories',
                'months_filter',
                'years',
                'currentYear'

            )
        );
    }

    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            $post = RencanaKerjaT::with('kategoriprogram', 'jenisprogram', 'aspekprogram')
                ->where('id_pengawas', Auth::user()->id)
                ->when($request->bln && $request->bln !== 'all', function($q) use ($request) {
                    return $q->where('bulan', $request->bln);
                })
                ->when($request->tahun && $request->tahun !== 'all', function($q) use ($request) {
                    return $q->where('tahun_ajaran', $request->tahun);
                })
                ->latest()->get();

            return Datatables::of($post)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d M Y H:i:s');
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
                ->addColumn('status_wa', function ($row) {
                    $log = WhatsappMessagesLog::where('rencana_kerja_id', $row->id)->latest()->first();
                    if ($row->status == 1 || ($log && $log->is_sent)) {
                        return '<span class="badge bg-label-success">Terkirim</span>';
                    }
                    $reason = ($log && !empty($log->failure_reason)) ? '<br><small class="text-danger">Gagal: ' . e($log->failure_reason) . '</small>' : '';
                    return '<span class="badge bg-label-danger">Gagal/Belum</span>' . $reason;
                })
                ->addColumn('nama_sekolah', function ($row) {
                    if ($row->is_mandiri == 1) {
                        return '<span class="badge bg-label-info">Mandiri (Refleksi)</span>';
                    }
                    $sekolahIds = explode(',', $row->sekolah_id);
                    $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();
                    $nama_sekolah = '';
                    foreach ($sekolahs as $sekolah) {
                        $nama_sekolah .= '<span class="badge bg-label-primary m-1">' . $sekolah->nama_sekolah . '</span> ';
                    }
                    return $nama_sekolah;
                })
                ->addColumn('deskripsi', function ($row) {
                    return Str::limit(strip_tags($row->deskripsi_permasalahan), 50);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a onclick="editPerencanaan(' . $row->id . ')" class="btn btn-sm bg-info text-white me-1"><i class="fa fa-edit"></i> Edit</a>';
                    $btn .= '<a href="#" onclick="deletePerencanaan(' . $row->id . ')" class="btn btn-danger btn-sm deletePost"><i class="fa fa-remove"></i> Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'nama_kategori', 'nama_jenis', 'nama_aspek', 'nama_sekolah', 'bulan_tahun', 'status_wa'])
                ->make(true);
        }
    }

    public function save(Request $request)
    {
        $sekolah_id_input = $request->post('sekolah_id');
        $sekolah_ids = is_array($sekolah_id_input) ? implode(',', $sekolah_id_input) : '';
        $kategori_id = $request->post('kategoriprogram_id');
        $umpanbalik_cat = $request->post('id_umpanbalik_category');
        
        // Jika RHK 3 ATAU Umpan Balik bukan Default (0), maka dianggap Mandiri
        $is_mandiri = ($kategori_id == 11 || ($umpanbalik_cat != 0 && !empty($umpanbalik_cat))) ? 1 : 0;

        // Validasi backend: Penguncian aspek dari Stakeholder
        $aspekIdInput = $request->post('aspekprogram_id');
        $user = Auth::user();
        if ($user && $user->role == 'Pengawas' && $aspekIdInput) {
            $userKabId = $user->kabupaten_id;
            $userJenjangs = json_decode($user->akses_jenjang, true) ?? [];
            $cMonth = $request->post('bulan', date('n'));
            $cYear = date('Y');

            $isClosed = PengaturanAspekStakeholder::where(function ($q) use ($userKabId) {
                if ($userKabId) {
                    $q->where('kabupaten_id', $userKabId)->orWhereNull('kabupaten_id');
                }
            })->where(function ($q) use ($user) {
                $q->where('pengawas_id', $user->id)->orWhereNull('pengawas_id');
            })->where(function ($q) use ($userJenjangs) {
                if (!empty($userJenjangs) && !in_array('All', $userJenjangs)) {
                    $q->whereIn('jenjang', $userJenjangs)->orWhereNull('jenjang');
                }
            })->where(function ($q) use ($cMonth) {
                $q->where('bulan', $cMonth)->orWhereNull('bulan');
            })->where(function ($q) use ($cYear) {
                $q->where('tahun', $cYear)->orWhereNull('tahun');
            })->where('aspek_program_id', $aspekIdInput)
              ->where('is_active', 0)
              ->exists();

            if ($isClosed) {
                return redirect()->back()->withInput()->with('error', 'Aspek Raport Pendidikan yang dipilih sedang DITUTUP oleh Stakeholder untuk periode ini.');
            }
        }

        try {
            $model = new RencanaKerjaT();
            \DB::transaction(function () use ($request, $sekolah_ids, $is_mandiri, $model) {
                $model->tahun_ajaran = date('Y');
                $model->id_pengawas = Auth::user()->id;
                $model->nama_program_kerja = $request->post('nama_program_kerja');
                $model->kategoriprogram_id = $request->post('kategoriprogram_id');
                $model->jenisprogram_id = $request->post('jenisprogram_id');
                $model->aspekprogram_id = $request->post('aspekprogram_id');
                $model->bulan = $request->post('bulan');
                $model->sekolah_id = $sekolah_ids;
                $model->is_mandiri = $is_mandiri;
                $model->deskripsi_permasalahan = $request->post('deskripsi_permasalahan');
                $model->target_capaian = $request->post('target_capaian');
                $model->tenggat_waktu = $request->post('tenggat_waktu');
                $model->id_umpanbalik_category = $request->post('id_umpanbalik_category');
                $model->save();
            });

            // Proses pengiriman WA di luar DB Transaction agar tidak menahan lock MySQL / menyebabkan timeout
            try {
                $this->kirimWa($model->id);
                return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil disimpan dan pesan WA terkirim!');
            } catch (\Exception $e) {
                return redirect()->route('pengawas.perencanaan')->with('error', 'Perencanaan berhasil disimpan, namun pesan WA GAGAL dikirim. Error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat rencana kerja: ' . $e->getMessage());
        }
    }

    //update
    public function update(Request $request)
    {
        $data = RencanaKerjaT::findOrFail($request->post('id'));
        $sekolah_id_input = $request->post('sekolah_id');
        $sekolah_ids = is_array($sekolah_id_input) ? implode(',', $sekolah_id_input) : '';
        $kategori_id = $request->post('kategoriprogram_id');
        $umpanbalik_cat = $request->post('id_umpanbalik_category');
        
        // Jika RHK 3 ATAU Umpan Balik bukan Default (0), maka dianggap Mandiri
        $is_mandiri = ($kategori_id == 11 || ($umpanbalik_cat != 0 && !empty($umpanbalik_cat))) ? 1 : 0;

        $data->tahun_ajaran = date('Y');
        $data->id_pengawas = Auth::user()->id;
        $data->nama_program_kerja = $request->post('nama_program_kerja');
        $data->kategoriprogram_id = $request->post('kategoriprogram_id');
        $data->sekolah_id = $sekolah_ids;
        $data->is_mandiri = $is_mandiri;
        $data->jenisprogram_id = $request->post('jenisprogram_id');
        $data->aspekprogram_id = $request->post('aspekprogram_id');
        $data->bulan = $request->post('bulan');
        $data->deskripsi_permasalahan = $request->post('deskripsi_permasalahan');
        $data->target_capaian = $request->post('target_capaian');
        $data->tenggat_waktu = $request->post('tenggat_waktu');
        $data->id_umpanbalik_category = $request->post('id_umpanbalik_category');
        $data->save();

        // Sinkronisasi umpan balik dan kirim ulang WA jika perlu
        try {
            $this->kirimWa($data->id);
            return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil diedit!');
        } catch (\Exception $e) {
            return redirect()->route('pengawas.perencanaan')->with('error', 'Perencanaan berhasil diedit, namun pesan WA GAGAL dikirim. Error: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        // Ambil data dari model berdasarkan ID atau yang lain sesuai kebutuhan
        $data = RencanaKerjaT::findOrFail($id);
        $umpanbalikCategories = UmpanbalikCategory::where('status', true)->get();

        return response()->json([
            'data' => $data,
            'umpanbalikCategories' => $umpanbalikCategories
        ]);
    }


    public function hapus($id)
    {
        // Temukan data yang akan dihapus
        $data = RencanaKerjaT::findOrFail($id);

        // Lakukan operasi penghapusan data
        $data->delete();

        // Balas dengan respons yang sesuai
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }

    public function kirimWa($id)
    {
        $model = RencanaKerjaT::with('pengawasnama')->findOrFail($id);
        $id_umpanbalik_category = $model->id_umpanbalik_category;

        if ($model->is_mandiri == 1) {
            $pengawas = User::with('profile')->find($model->id_pengawas);
            $no_telp = $pengawas->no_telp;
            if (empty($no_telp) && $pengawas->profile) {
                $no_telp = $pengawas->profile->no_telp;
            }

            if (empty($no_telp)) {
                throw new \Exception("Anda belum mengisi nomor HP di profil.");
            }

            $this->buildMandiriUmpanBalik($model, $pengawas->name, $pengawas->id, $no_telp, $id_umpanbalik_category);
        } else {
            $sekolahIds = explode(',', $model->sekolah_id);
            $sekolahs = SekolahM::with('kepalaSekolahSatu')->whereIn('id', $sekolahIds)->get();

            if ($sekolahs->isEmpty()) {
                throw new \Exception("Tidak ada sekolah sasaran yang dipilih.");
            }

            $validUserIds = $sekolahs->pluck('kepalaSekolahSatu.id')->filter()->toArray();
            UmpanbalikT::where('id_pelaporan', $model->id)
                ->whereNotIn('id_user', $validUserIds)
                ->whereNull('submitted_at')
                ->delete();

            foreach ($sekolahs as $list) {
                $kepalaSekolah = $list->kepalaSekolahSatu;
                if ($kepalaSekolah && !empty($kepalaSekolah->no_telp)) {
                    if ($id_umpanbalik_category == 0) {
                        $this->buildUmpanBalik($model, $list->nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp);
                    } else {
                        $this->buildDynamicUmpanBalik($model, $list->nama_sekolah, $kepalaSekolah->nama, $kepalaSekolah->id, $kepalaSekolah->no_telp, $id_umpanbalik_category);
                    }
                } else {
                    Log::warning("[Perencanaan] Kepala sekolah {$list->nama_sekolah} tidak memiliki nomor telepon.");
                }
            }
        }

        $model->status = 2;
        $model->save();
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

        $ref = date('YmdHis') . rand(100, 999);
        $pesan = "Halo Bapak/Ibu {$nama_pengawas},\n"
            . "Anda telah membuat Rencana Kerja Mandiri: {$model->nama_program_kerja}.\n"
            . "Silakan isi umpan balik/refleksi mandiri pada link berikut:{$fullUrl}\n\n"
            . "Ref : {$ref}\n"
            . "Terimakasih";

        $this->dispatchWaJob($no_telp, $pesan, $model, null);
    }

    public function buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp)
    {
        $checkUmpanBalik = UmpanbalikT::where('id_user', $nama_kepala_sekolah_id)
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->first();

        if ($checkUmpanBalik) {
            $umpanBalik = $checkUmpanBalik;
            $umpanBalik->id_updated_by = Auth::user()->id;
            $umpanBalik->save();
            $fullUrl = url('umpan-balik/' . $checkUmpanBalik->generate_url);
        } else {
            $uniqueUrl = Str::uuid()->getHex();
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

        $nama_pengawas = $model->pengawasnama ? $model->pengawasnama->name : 'Pengawas';
        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran} pengawas {$nama_pengawas} akan melakukan kegiatan pengawasan {$model->nama_program_kerja} ke sekolah.\n"
            . "Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "Terimakasih";

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
            $generate_url = (string) \Illuminate\Support\Str::uuid();
            UmpanbalikT::create([
                'id_user' => $nama_kepala_sekolah_id,
                'id_pelaporan' => $model->id,
                'generate_url' => $generate_url,
                'id_pengawas' => $model->id_pengawas,
                'id_category' => $id_category,
                'id_created_by' => Auth::user()->id,
                'id_updated_by' => Auth::user()->id,
                'tgl_rtl' => date('Y-m-d'),
            ]);
            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $generate_url]);
        }

        $nama_pengawas = $model->pengawasnama ? $model->pengawasnama->name : 'Pengawas';
        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran} pengawas {$nama_pengawas} akan melakukan kegiatan pengawasan {$model->nama_program_kerja} ke sekolah.\n"
            . "Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "Terimakasih";

        $this->dispatchWaJob($no_telp, $pesan, $model, $nama_kepala_sekolah_id);
    }

    /**
     * Validasi nomor, siapkan pesan, lalu dispatch ke queue (async).
     */
    protected function dispatchWaJob($phone, $message, $model, $kepalaSekolahId)
    {
        $validation = WaBlastSafetyService::validatePhoneNumber($phone);
        if (!$validation['valid']) {
            Log::warning("[Perencanaan WA] Nomor tidak valid ({$phone}): {$validation['reason']}");
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
            Log::error("[Perencanaan WA] Pesan kosong untuk nomor {$phone}");
            return;
        }

        $message = WaBlastSafetyService::prepareMessageBody($message);

        \App\Jobs\SendWhatsappMessageJob::dispatch(
            $phone,
            $message,
            $model->id,
            $kepalaSekolahId
        );

        Log::info("[Perencanaan WA] Job diantri untuk {$phone} (rencana_kerja_id: {$model->id})");
    }

    public function exportPDF(Request $request)
    {
        $user = User::with('profile')->find(Auth::user()->id);
        $post = RencanaKerjaT::with('kategoriprogram', 'jenisprogram', 'aspekprogram')
            ->where('id_pengawas', Auth::user()->id)
            ->when($request->bln && $request->bln !== 'all', function($q) use ($request) {
                return $q->where('bulan', $request->bln);
            })
            ->when($request->tahun && $request->tahun !== 'all', function($q) use ($request) {
                return $q->where('tahun_ajaran', $request->tahun);
            })
            ->oldest()
            ->get();

        $bln = $request->bln && $request->bln !== 'all' ? $request->bln : null;
        $tahun = $request->tahun && $request->tahun !== 'all' ? $request->tahun : null;
        $periode = ($bln ? $bln : 'Semua Bulan') . ' ' . ($tahun ? $tahun : 'Semua Tahun');

        $pdf = \PDF::loadView('dashboard_pengawas.perencanaan.export_pdf', [
            'user' => $user,
            'data' => $post,
            'generateDate' => date('d M Y H:i:s'),
            'periode' => $periode,
        ]);

        return $pdf->download('Rencana_Kerja_' . Str::slug($user->name) . '_' . date('YmdHis') . '.pdf');
    }
}
