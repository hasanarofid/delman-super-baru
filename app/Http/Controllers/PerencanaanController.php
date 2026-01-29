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
use Carbon\Carbon;
class PerencanaanController extends Controller
{
    //index
    public function index()
    {
        $kegiatan = TugaskerjaT::with('tugas')
            ->where('id_pengawas', Auth::user()->id)->get();
        $kategory = Kategory::where('type', 'pelaporan')->get();
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
        $umpanbalikCategories = UmpanbalikCategory::where('status', true)->get();

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
                'umpanbalikCategories'

            )
        );
    }

    public function getdata(Request $request)
    {
        if ($request->ajax()) {
            $post = RencanaKerjaT::with('kategoriprogram', 'jenisprogram', 'aspekprogram')
                ->where('id_pengawas', Auth::user()->id)->latest()->get();

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
                    if ($row->status == 1) {
                        return '<span class="badge bg-label-success">Terkirim</span>';
                    } else {
                        $log = WhatsappMessagesLog::where('rencana_kerja_id', $row->id)->latest()->first();
                        $reason = $log ? '<br><small class="text-danger">Gagal: ' . $log->failure_reason . '</small>' : '';
                        return '<span class="badge bg-label-danger">Gagal/Belum</span>' . $reason;
                    }
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

        try {
            return \DB::transaction(function () use ($request, $sekolah_ids, $is_mandiri) {
                $model = new RencanaKerjaT();
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

                // Proses pengiriman WA (Jika ini gagal, transaction akan rollback)
                $this->kirimWa($model->id);

                return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil disimpan dan pesan WA terkirim!');
            });
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
        $this->kirimWa($data->id);

        return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil diedit!');
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
            // Logika untuk Mandiri (RHK 3)
            $pengawas = User::with('profile')->find(Auth::user()->id);
            
            // Cek nomor telpon di tabel users dulu, jika kosong cek di profile
            $no_telp = $pengawas->no_telp;
            if (empty($no_telp) && $pengawas->profile) {
                $no_telp = $pengawas->profile->no_telp;
            }

            $nama_pengawas = $pengawas->name;
            $id_pengawas = $pengawas->id;

            if (empty($no_telp)) {
                throw new \Exception("Anda belum mengisi nomor HP di profil.");
            }

            // Gunakan buildDynamicUmpanBalik khusus untuk mandiri
            $this->buildMandiriUmpanBalik($model, $nama_pengawas, $id_pengawas, $no_telp, $id_umpanbalik_category);
        } else {
            // Logika lama untuk Sekolah (RHK 1 & 2)
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
                $nama_sekolah = $list->nama_sekolah;
                $kepalaSekolah = $list->kepalaSekolahSatu;

                if ($kepalaSekolah && !empty($kepalaSekolah->no_telp)) {
                    $nama_kepala_sekolah = $kepalaSekolah->nama;
                    $nama_kepala_sekolah_id = $kepalaSekolah->id;
                    $no_telp = $kepalaSekolah->no_telp;

                    if ($id_umpanbalik_category == 0) {
                        $this->buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp);
                    } else {
                        $this->buildDynamicUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp, $id_umpanbalik_category);
                    }

                    sleep(rand(2, 5));
                } else {
                    throw new \Exception("Kepala sekolah {$nama_sekolah} tidak memiliki nomor telepon.");
                }
            }
        }

        $model->status = 1;
        $model->save();
    }

    public function buildMandiriUmpanBalik($model, $nama_pengawas, $id_pengawas, $no_telp, $id_category)
    {
        // Untuk mandiri, id_user diisi dengan ID Pengawas (User)
        // Kita perlu memastikan model UmpanbalikT bisa menerima ID User dari tabel users,
        // namun biasanya id_user di model ini merujuk ke tabel guru_m (kepala sekolah).
        // Mari kita cek tabel umpanbalik_t untuk melihat relasi id_user.
        
        $checkUmpanBalik = UmpanbalikT::where('id_user_pengawas', $id_pengawas) // Tambahkan kolom baru id_user_pengawas jika perlu
            ->where('id_pelaporan', $model->id)
            ->where('id_pengawas', $model->id_pengawas)
            ->where('id_category', $id_category)
            ->first();

        // Karena id_user biasanya adalah GuruM, kita mungkin butuh kolom baru atau penanda.
        // Opsi lain: Gunakan id_user tetapi simpan ID dari tabel users.
        
        $generate_url = (string) \Illuminate\Support\Str::uuid();
        if (!$checkUmpanBalik) {
            UmpanbalikT::create([
                'id_user' => 0, // Tandai sebagai bukan GuruM
                'id_user_pengawas' => $id_pengawas, // Kolom baru
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

        $pesan = "Halo Pak/Bu {$nama_pengawas},\n"
            . "Anda telah membuat Rencana Kerja Mandiri: {$model->nama_program_kerja}.\n"
            . "Silakan isi umpan balik/refleksi mandiri pada link berikut: {$fullUrl}\n\n"
            . "Terimakasih\n"
            . "DelmanSuper Platform";

        $this->sendWhatsAppMessage($no_telp, $pesan, null, $model);
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
        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}
Kepala {$nama_sekolah},
Pada bulan {$model->bulan} {$model->tahun_ajaran} pengawas {$nama_pengawas} akan melakukan kegiatan pendampingan {$model->nama_program_kerja} ke sekolah.
Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}

Berikut ini beberapa catatan yang penting:
1. Pastikan link diisi pada hari pengawas melakukan pendampingan.
2. Sertakan 1 bukti pendampingan berupa foto kegiatan bersama pengawas.

Terimakasih
Pesan ini digenerate otomatis oleh Sistem Monitoring dan Evaluasi Digital Pengawas (DelmanSuper)";

        $pesan = trim($pesan);
        $this->sendWhatsAppMessage($no_telp, $pesan, $nama_kepala_sekolah_id, $model);
    }

    // protected function sendWhatsAppMessage($phone, $message,$nama_kepala_sekolah_id,$model)
    // {
    //     $token = 'OZ9q0PSQUUV4PRZGxyKUfZjt9EFyt22dTIRnklQSepTmFlrFMN9BqaIs7RXtnD9I';
    //     $url = "https://jogja.wablas.com/api/send-message";

    //     $logEntry = new WhatsappMessagesLog();
    //     $logEntry->rencana_kerja_id = $model->id; // Add the Rencana Kerja ID here
    //     $logEntry->kepala_sekolah_id = $nama_kepala_sekolah_id;
    //     $logEntry->phone_number = $phone;
    //     $logEntry->message = $message;

    //     try {
    //         $response = Http::withHeaders([
    //             'Authorization' => $token,
    //         ])->post($url, [
    //             'phone' => $phone,
    //             'message' => $message,
    //         ]);

    //         if ($response->successful()) {
    //             Log::info("WhatsApp message sent successfully to {$phone}");
    //             $logEntry->is_sent = true;
    //         } else {
    //             Log::error("Failed to send WhatsApp message to {$phone}: " . $response->body());
    //             $logEntry->is_sent = false;
    //             $logEntry->failure_reason = "Failed to send message: " . $response->body();
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("WhatsApp API error for {$phone}: " . $e->getMessage());
    //         $logEntry->is_sent = false;
    //         $logEntry->failure_reason = "API error: " . $e->getMessage();
    //     }

    //     $logEntry->save(); // Save the log entry to the database
    // }
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
                'tgl_rtl' => date('Y-m-d'), // Added missing tgl_rtl
            ]);

            $fullUrl = route('dynamic.umpanbalik.form', ['id_category' => $id_category, 'generate_url' => $generate_url]);
        }

        $nama_pengawas = $model->pengawasnama ? $model->pengawasnama->name : 'Pengawas';
        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran}\n"
            . "pengawas {$nama_pengawas}\n"
            . "akan melakukan kegiatan pendampingan {$model->nama_program_kerja}\n"
            . "ke sekolah.\n"
            . "Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pendampingan.\n"
            . "2. Sertakan 1 bukti pendampingan berupa foto kegiatan bersama pengawas.\n\n"
            . "Terimakasih\n"
            . "Pesan ini digenerate otomatis oleh Sistem Monitoring dan Evaluasi Digital Pengawas (DelmanSuper)";

        $this->sendWhatsAppMessage($no_telp, $pesan, $nama_kepala_sekolah_id, $model);
    }

    protected function sendWhatsAppMessage($phone, $message, $nama_kepala_sekolah_id, $model)
    {
        $token = 'ChvMJmr8Y5PwD130iY6kZqNQoAvCNQBxvH4RKiCOckJCAvEtVZtBO2Gyubj9THyU';
        $secretKey = 'SqKDZXzk';
        $url = "https://jogja.wablas.com/api/send-message";

        // Format nomor telepon
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }

        if (empty(trim($message))) {
            Log::error("WhatsApp message is empty for phone {$phone}");
            return;
        }

        // Branding Replacement
        $message = str_ireplace(['simodip', 'sistem modip', 'Sistem Monitoring dan Evaluasi Digital Pengawas'], 'DelmanSuper', $message);

        $logEntry = new WhatsappMessagesLog();
        $logEntry->rencana_kerja_id = $model->id;
        $logEntry->kepala_sekolah_id = $nama_kepala_sekolah_id;
        $logEntry->phone_number = $phone;
        $logEntry->message = $message;

        try {
            $data = ['phone' => $phone, 'message' => $message];

            // Format 1: token.secretKey
            $authorization = "{$token}.{$secretKey}";
            $response = Http::withHeaders(['Authorization' => $authorization])->asForm()->post($url, $data);

            if ($response->successful()) {
                $logEntry->is_sent = true;
                $logEntry->save();
                return true;
            }

            // Format 2: secret in data (Jika 403 atau gagal format 1)
            $dataWithSecret = array_merge($data, ['secret' => $secretKey]);
            $response2 = Http::withHeaders(['Authorization' => $token])->asForm()->post($url, $dataWithSecret);

            if ($response2->successful()) {
                $logEntry->is_sent = true;
                $logEntry->save();
                return true;
            }

            // Format 3: token only
            $response3 = Http::withHeaders(['Authorization' => $token])->asForm()->post($url, $data);

            if ($response3->successful()) {
                $logEntry->is_sent = true;
                $logEntry->save();
                return true;
            }

            // Semua gagal
            $responseBody = $response3->body();
            $resArr = json_decode($responseBody, true);
            $errMsg = $resArr['message'] ?? $responseBody;

            if (strpos($responseBody, 'IP') !== false) {
                $errMsg .= " (IP Server belum di-whitelist di Wablas)";
            }

            $logEntry->is_sent = false;
            $logEntry->failure_reason = "Semua format auth gagal. Terakhir: " . $errMsg;
            $logEntry->save();

            throw new \Exception($errMsg);

        } catch (\Exception $e) {
            $logEntry->is_sent = false;
            $error_message = $e->getMessage();
            $logEntry->failure_reason = substr($error_message, 0, 250); // Truncate to fit column
            $logEntry->save();
            throw $e;
        }
    }


    // protected function sendWhatsAppMessage($phone, $message)
    // {
    //     $token = 'OZ9q0PSQUUV4PRZGxyKUfZjt9EFyt22dTIRnklQSepTmFlrFMN9BqaIs7RXtnD9I';
    //     $url = "https://jogja.wablas.com/api/send-message";

    //     try {
    //         $response = Http::withHeaders([
    //             'Authorization' => $token,
    //         ])->post($url, [
    //             'phone' => $phone,
    //             'message' => $message,
    //         ]);

    //         if ($response->successful()) {
    //             Log::info("WhatsApp message sent successfully to {$phone}");
    //         } else {
    //             Log::error("Failed to send WhatsApp message to {$phone}: " . $response->body());
    //         }

    //     } catch (\Exception $e) {
    //         Log::error("WhatsApp API error for {$phone}: " . $e->getMessage());
    //     }
    // }

}
