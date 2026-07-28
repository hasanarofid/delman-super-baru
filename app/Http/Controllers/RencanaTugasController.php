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
                        return response()->json(['success' => false, 'message' => "Kepala sekolah {$nama_sekolah} tidak memiliki nomor telepon."], 400);
                    }
                }
            }
            $model->status = 1;
            $model->save();

        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal kirim WA: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Pesan WA berhasil dikirim!']);
    }

    public function kirimWaWithCategory($id, $id_category)
    {
        try {
            $model = RencanaKerjaT::findOrFail($id);
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
                    $nama_sekolah = $list->nama_sekolah;
                    $kepalaSekolah = $list->kepalaSekolahSatu;
                    if ($kepalaSekolah) {
                        $nama_kepala_sekolah = $kepalaSekolah->nama;
                        $nama_kepala_sekolah_id = $kepalaSekolah->id;
                        $no_telp = $kepalaSekolah->no_telp;

                        if ($id_category == 0) {
                            // Panggil fungsi buildUmpanBalik yang lama untuk URL statis
                            $this->buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp);
                            sleep(rand(30, 60));
                        } else {
                            // Memanggil fungsi buildDynamicUmpanBalik untuk URL dinamis
                            $this->buildDynamicUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp, $id_category);
                            sleep(rand(30, 60));
                        }
                    }
                }
            }
            $model->status = 1;
            $model->save();

            return response()->json(['success' => true, 'message' => 'WA message with dynamic feedback sent successfully!']);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message with dynamic feedback: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send WA message.'], 500);
        }
    }

    //kirimwakepalasekolah
    public function kirimWaSekolah($id, $id_user)
    {
        try {
            $model = RencanaKerjaT::findOrFail($id);
            
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
                $nama_sekolah = $kepalaSekolah->sekolah->nama_sekolah;
                $nama_kepala_sekolah = $kepalaSekolah->nama;
                $nama_kepala_sekolah_id = $kepalaSekolah->id;
                $no_telp = $kepalaSekolah->no_telp;

                // Memanggil fungsi buildUmpanBalik yang lama untuk URL statis
                $this->buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp);
            }

            $model->status = 1;
            $model->save();
            
            return response()->json(['success' => true, 'message' => 'WA message sent successfully!']);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal kirim WA: ' . $e->getMessage()], 500);
        }
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

        $pesan = "Halo Pak/Bu {$nama_pengawas},\n"
            . "Anda telah membuat Rencana Kerja Mandiri: {$model->nama_program_kerja}.\n"
            . "Silakan isi umpan balik/refleksi mandiri pada link berikut: {$fullUrl}\n\n"
            . "Terimakasih\n"
            . "DelmanSuper Platform";

        $this->sendWhatsAppMessage($no_telp, $pesan, null, $model);
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

        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran}\n"
            . "pengawas {$model->pengawasnama->name}\n"
            . "akan melakukan kegiatan pengawasan {$model->nama_program_kerja}\n"
            . "ke sekolah.\n"
            . "Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "Terimakasih\n"
            . "Pesan ini digenerate otomatis oleh DelmanSuper ";

        $this->sendWhatsAppMessage($no_telp, $pesan, $nama_kepala_sekolah_id, $model);
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

        $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}\n"
            . "Kepala {$nama_sekolah},\n"
            . "Pada bulan {$model->bulan} {$model->tahun_ajaran}\n"
            . "pengawas {$model->pengawasnama->name}\n"
            . "akan melakukan kegiatan pengawasan {$model->nama_program_kerja}\n"
            . "ke sekolah.\n"
            . "Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}\n\n"
            . "Berikut ini beberapa catatan yang penting:\n"
            . "1. Pastikan link diisi pada hari pengawas melakukan pengawasan.\n"
            . "2. Sertakan 1 bukti pengawasan berupa foto kegiatan bersama pengawas.\n\n"
            . "Terimakasih\n"
            . "Pesan ini digenerate otomatis oleh DelmanSuper ";

        $this->sendWhatsAppMessage($no_telp, $pesan, $nama_kepala_sekolah_id, $model);
    }


    protected function sendWhatsAppMessage($phone, $message, $nama_kepala_sekolah_id, $model)
    {
        // Rate limiting & Warming-up check
        $safetyCheck = WaBlastSafetyService::checkCanSend();
        if (!$safetyCheck['allowed']) {
            Log::warning($safetyCheck['message']);
            throw new \Exception($safetyCheck['message']);
        }

        // Apply dynamic safety delay based on warming-up phase
        WaBlastSafetyService::applySafetyDelay();

        $token = config('services.wablas.token') ?: env('WABLAS_TOKEN');
        $secretKey = config('services.wablas.secret') ?: env('WABLAS_SECRET');
        $url = config('services.wablas.endpoint') ?: env('WABLAS_ENDPOINT', 'https://jogja.wablas.com/api/send-message');

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

        // Branding & Anti-Spam Suffix
        $message = WaBlastSafetyService::prepareMessageBody($message);

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
            if (empty($logEntry->failure_reason)) {
                $logEntry->failure_reason = $e->getMessage();
            }
            $logEntry->save();
            throw $e;
        }
    }
}
