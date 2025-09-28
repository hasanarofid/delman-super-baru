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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class PerencanaanController extends Controller
{
    //index
    public function index(){
        $kegiatan = TugaskerjaT::with('tugas')
        ->where('id_pengawas',Auth::user()->id)->get();
        $kategory = Kategory::where('type','perencanaan')->get();
        $subkategory = [];
        $binaan = SekolahbinaanT::with('sekolah')
        ->where('id_pengawas',Auth::user()->id)->get();
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
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }
        $jenisProgram = JenisProgram::where('status',true)->get();
        $aspekProgram = AspekProgram::where('status',true)->get();

        return view('dashboard_pengawas.perencanaan.index',
        compact('kegiatan'
        ,'kategory','subkategory','binaan','months',
        'jenisProgram','aspekProgram'

    ));
    }

     // get data perencanaa
     public function getdata(Request $request){
        if ($request->ajax()) {


         $post = RencanaKerjaT::with('kategoriprogram','jenisprogram','aspekprogram')
         ->where('id_pengawas',Auth::user()->id)->latest()->get();

            return Datatables::of($post)
                    ->addIndexColumn()
            //          ->addColumn('foto', function($row){
            //             if($row->foto == 'userdefault.jpg'){
            //                 $foto = asset('userdefault.jpg');
            //             }else{
            //                 $foto =  route('laporan',$row->foto );
            //             }

            //          return  ' <div class="card card-profile"><img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top"></div>';
            //         })->addColumn('tugas', function($row){
            //             return !empty($row->tugaskerja->tugas->kegiatan) ? $row->tugaskerja->tugas->kegiatan: '-';
            ->addColumn('tanggal', function($row){
                return $row->created_at->format('d M Y h:i:s');
            })
            ->addColumn('nama_kategori', function($row){
                return $row->kategoriprogram->nama;
            })

            ->addColumn('nama_jenis', function($row){
                return !empty($row->jenisprogram->nama) ? $row->jenisprogram->nama : '-';
            })

            ->addColumn('nama_aspek', function($row){
                return !empty($row->aspekprogram->nama) ? $row->aspekprogram->nama : '-';
            })
            ->addColumn('bulan_tahun', function($row){
                return $row->bulan .' - '. $row->tahun_ajaran;
            })

             ->addColumn('nama_sekolah', function($row){
                $sekolahIds = explode(',', $row->sekolah_id);
                $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();

                $nama_sekolah = '';
                foreach ($sekolahs as $sekolah) {
                    $nama_sekolah .= '<span class="badge bg-label-primary m-1">' . $sekolah->nama_sekolah . '</span> '; // Added a space for separation
                }
                // dd($nama_sekolah);

                return $nama_sekolah;
            })

            ->addColumn('action', function($row){

                           $btn = '<a  onclick="editPerencanaan('.$row->id.')" class="btn btn-sm bg-info text-white " > <i class="fa fa-edit"></i> Edit</a>';
                           $btn = $btn. '<a href="#" onclick="deletePerencanaan('.$row->id.')" class="btn btn-danger btn-sm deletePost"><i class="fa fa-remove"></i> Delete</a>';

                            return $btn;
                    })
                    ->rawColumns(['action',
                    'nama_kategori',
                    'nama_jenis',
                    'nama_aspek',
                    'nama_sekolah',
                    'bulan_tahun'])
                    ->make(true);
        }
    }

    public function save(Request $request){
        // dd($request->post());
        $sekolah_ids = implode(',', $request->post('sekolah_id'));
        $model = new RencanaKerjaT();
        $model->tahun_ajaran = date('Y');
        $model->id_pengawas = Auth::user()->id;
        $model->nama_program_kerja = $request->post('nama_program_kerja');
        $model->kategoriprogram_id = $request->post('kategoriprogram_id');
        $model->jenisprogram_id = $request->post('jenisprogram_id');
        $model->aspekprogram_id = $request->post('aspekprogram_id');
        $model->bulan = $request->post('bulan');
        $model->sekolah_id = $sekolah_ids;
        $model->deskripsi_permasalahan = $request->post('deskripsi_permasalahan');
        $model->target_capaian = $request->post('target_capaian');
        $model->tenggat_waktu = $request->post('tenggat_waktu');
        $model->save();
        $this->kirimWa($model->id);
        return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil disimpan!');
    }

    //update
    public function update(Request $request){
        $data = RencanaKerjaT::findOrFail($request->post('id'));
        // dd($request->post());
        $sekolah_ids = implode(',', $request->post('sekolah_id'));
        $data->tahun_ajaran = date('Y');
        $data->id_pengawas = Auth::user()->id;
        $data->nama_program_kerja = $request->post('nama_program_kerja');
        $data->kategoriprogram_id = $request->post('kategoriprogram_id');
        $data->sekolah_id = $sekolah_ids;
        $data->kategoriprogram_id = $request->post('kategoriprogram_id');
        $data->jenisprogram_id = $request->post('jenisprogram_id');
        $data->aspekprogram_id = $request->post('aspekprogram_id');
        $data->bulan = $request->post('bulan');
        $data->deskripsi_permasalahan = $request->post('deskripsi_permasalahan');
        $data->target_capaian = $request->post('target_capaian');
        $data->tenggat_waktu = $request->post('tenggat_waktu');
        $data->save();
        return redirect()->route('pengawas.perencanaan')->with('success', 'Perencanaan berhasil diedit!');
    }


    public function edit($id)
    {
        // Ambil data dari model berdasarkan ID atau yang lain sesuai kebutuhan
        $data = RencanaKerjaT::findOrFail($id); // Gantilah YourModel dengan model yang sesuai

        return response()->json($data);
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

    public function buildUmpanBalik($model, $nama_sekolah, $nama_kepala_sekolah, $nama_kepala_sekolah_id, $no_telp)
    {
        try {



            $checkUmpanBalik = UmpanbalikT::where('id_user',$nama_kepala_sekolah_id)
            ->where('id_pelaporan',$model->id)
            ->where('id_pengawas',$model->id_pengawas)
            ->first();
                if($checkUmpanBalik){
                    $umpanBalik = $checkUmpanBalik;
                    $umpanBalik->id_updated_by = Auth::user()->id;
                    $umpanBalik->save();
                    $fullUrl = url('umpan-balik/' . $checkUmpanBalik->generate_url);
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
