<?php

namespace App\Http\Controllers;

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

class RencanaTugasController extends Controller
{
    //index
    public function index(){
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

        $listPengawas = User::where('role','pengawas')->get();
        return view('rencanakerja.index',
        compact('listPengawas',
        'months',
        'currentYear',
        'years',
    ));
    }

    public function getdata(Request $request) {
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
                ->addColumn('pengawas', function($row) {
                    return $row->pengawasnama->nip . ' - ' . $row->pengawasnama->name;
                })
                ->addColumn('tanggal', function($row) {
                    return $row->created_at->format('d M Y h:i:s');
                })
                ->addColumn('nama_kategori', function($row) {
                    return $row->kategoriprogram->nama;
                })
                ->addColumn('nama_jenis', function($row) {
                    return !empty($row->jenisprogram->nama) ? $row->jenisprogram->nama : '-';
                })
                ->addColumn('nama_aspek', function($row) {
                    return !empty($row->aspekprogram->nama) ? $row->aspekprogram->nama : '-';
                })
                ->addColumn('bulan_tahun', function($row) {
                    return $row->bulan . ' - ' . $row->tahun_ajaran;
                })
                ->addColumn('nama_sekolah', function($row) {
                    $sekolahIds = explode(',', $row->sekolah_id);
                    $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();

                    $nama_sekolah = '';
                    foreach ($sekolahs as $sekolah) {
                        $nama_sekolah .= '<span class="badge bg-label-primary m-1" data-sekolah2="' . $sekolah->nama_sekolah . '">' . $sekolah->nama_sekolah . '</span> ';
                    }
                    return $nama_sekolah;
                })
                ->addColumn('status', function($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-label-success m-1">Sudah Kirim WA Blast</span>'
                        : '<span class="badge bg-label-danger m-1">Belum Kirim WA Blast</span>';
                })
                ->addColumn('action', function($row) {
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
            $model = RencanaKerjaT::findOrFail($id);
            $sekolahIds = explode(',', $model->sekolah_id);

            $sekolahs = SekolahM::with('kepalaSekolahSatu')->whereIn('id', $sekolahIds)->get();

            foreach ($sekolahs as $list) {
                $nama_sekolah = $list->nama_sekolah;
                $kepalaSekolah = $list->kepalaSekolahSatu;
                if ($kepalaSekolah) {
                    $nama_kepala_sekolah = $kepalaSekolah->nama;
                    $nama_kepala_sekolah_id = $kepalaSekolah->id;
                    $no_telp = $kepalaSekolah->no_telp;

                    $this->buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp);
                }
            }
            $model->status = 1;
            $model->save();
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message: " . $e->getMessage());
        }
    }

    //kirimwakepalasekolah
    public function kirimWaSekolah($id,$id_user){
        // dd($id_user);
        try {
            $umpan = UmpanBalikT::where('id_pelaporan',$id)
            ->where('id_user',$id_user)->first();

            $model = RencanaKerjaT::findOrFail($id);
            $kepalaSekolah = GuruM::findOrFail($id_user);
                $nama_sekolah = $kepalaSekolah->sekolah->nama_sekolah;
                     $nama_kepala_sekolah = $kepalaSekolah->nama;
                    $nama_kepala_sekolah_id = $kepalaSekolah->id;
                    $no_telp = $kepalaSekolah->no_telp;

                    $this->buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp);


            $model->status = 1;
            $model->save();
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message: " . $e->getMessage());
        }
    }

    public function buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp)
    {
        try {
            $uniqueUrl = Str::uuid()->toString();



            $checkUmpanBalik = UmpanbalikT::where('id_user',$nama_kepala_sekolah_id)
            ->where('id_pelaporan',$model->id)
            ->where('id_pengawas',$model->id_pengawas)
            ->first();
                if($checkUmpanBalik){
                    $umpanBalik = $checkUmpanBalik;
                    $umpanBalik->id_updated_by = Auth::user()->id;
                    $umpanBalik->save();
                    $fullUrl = url('umpan-balik/' . $umpanBalik->generate_url);
                }else{
                    $uniqueUrl = Str::uuid()->getHex();
                    $umpanBalik = new UmpanbalikT();
                    $umpanBalik->generate_url = $uniqueUrl;
                    $umpanBalik->id_updated_by = Auth::user()->id; // Set the updated_by field
                    $umpanBalik->id_pelaporan = $model->id;
                    $umpanBalik->id_user = $nama_kepala_sekolah_id;
                    $umpanBalik->id_pengawas = $model->id_pengawas;
                    $umpanBalik->generate_url = $uniqueUrl;
                    $umpanBalik->id_created_by = Auth::user()->id;
                    $umpanBalik->save();
                    $fullUrl = url('umpan-balik/' . $uniqueUrl);
                }






            $pesan = "Yth Bapak / Ibu {$nama_kepala_sekolah}
            Kepala {$nama_sekolah},
            Pada bulan {$model->bulan} {$model->tahun}
            pengawas {$model->pengawasnama->name}
            akan melakukan kegiatan pendampingan {$model->nama_program_kerja}
            ke sekolah.
            Mohon dapat mengisi formulir Monev pada link berikut : {$fullUrl}

            Berikut ini beberapa catatan yang penting:
            1. Pastikan link diisi pada hari pengawas melakukan pendampingan.
            2. Sertakan 1 bukti pendampingan berupa foto kegiatan bersama pengawas.

            Terimakasih
            Pesan ini digenerate otomatis oleh Sistem Monitoring dan Evaluasi Digital Pengawas (DelmanSuper) KCD Kabupaten Tangerang";

            $this->sendWhatsAppMessage($no_telp, $pesan,$nama_kepala_sekolah_id,$model);

        } catch (\Exception $e) {
            Log::error("Failed to create or send feedback link: " . $e->getMessage());
        }
    }


    protected function sendWhatsAppMessage($phone, $message, $nama_kepala_sekolah_id, $model)
    {
        $token = 'ChvMJmr8Y5PwD130iY6kZqNQoAvCNQBxvH4RKiCOckJCAvEtVZtBO2Gyubj9THyU';
        $secretKey = 'fv6GW850'; // Replace this with the actual secret key
        $authorization = "{$token}.{$secretKey}";
        $url = "https://jogja.wablas.com/api/send-message";

        $logEntry = new WhatsappMessagesLog();
        $logEntry->rencana_kerja_id = $model->id; // Add the Rencana Kerja ID here
        $logEntry->kepala_sekolah_id = $nama_kepala_sekolah_id;
        $logEntry->phone_number = $phone;
        $logEntry->message = $message;

        try {
            $response = Http::withHeaders([
                'Authorization' => $authorization,
            ])->post($url, [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to {$phone}");
                $logEntry->is_sent = true;
            } else {
                Log::error("Failed to send WhatsApp message to {$phone}: " . $response->body());
                $logEntry->is_sent = false;
                $logEntry->failure_reason = "Failed to send message: " . $response->body();
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp API error for {$phone}: " . $e->getMessage());
            $logEntry->is_sent = false;
            $logEntry->failure_reason = "API error: " . $e->getMessage();
        }

        $logEntry->save(); // Save the log entry to the database
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
