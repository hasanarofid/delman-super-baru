<?php

namespace App\Http\Controllers;

use App\GuruM;
use App\Models\Kategory;
use App\Models\TugaskerjaT;
use Illuminate\Http\Request;
use App\Models\Pelaporan;
use App\Models\RencanaKerjaT;
use App\Models\SekolahbinaanT;
use App\Models\SubKategory;
use App\Models\UmpanbalikT;
use App\SekolahM;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
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
        // dd($binaan);

        return view('dashboard_pengawas.pelaporan.index',
            compact('kegiatan', 'kategory', 'subkategory', 'binaan')
        );
    }

    // save pelaporan
    // public function save(Request $request){
    //     // dd($request->post());die;
    //     if ($request->hasFile('lampiran')) {
    //         $lampiran = $request->file('lampiran');

    //         // Generate a unique name based on the current date and time.
    //         $lampiranName = 'lampiran'.now()->format('YmdHis') . '_' . Str::random(10) . '.' . $lampiran->getClientOriginalExtension();

    //         // Store the image in the "blog" directory within the "public" disk.
    //         $request->lampiran->storeAs('lampiran', $lampiranName, 'public');

    //     }
    //             // $pelaporan->foto = $imageName;

    //     $pelaporan = new Pelaporan();
    //     $pelaporan->kategori = $request->post('kategoriprogram_id');
    //     $pelaporan->sub_kategori = $request->post('sub_kategori');
    //     $pelaporan->sasaran = $request->post('sasaran');
    //     $pelaporan->object = $request->post('object_sasaran');
    //     $pelaporan->tgl_pendampingan = $request->post('tgl_pendampingan');
    //     $pelaporan->judul = $request->post('judul');
    //     // $pelaporan->deskripsi_permasalahan = $request->post('deskripsi_permasalahan');
    //     // $pelaporan->target_capaian = $request->post('target_capaian');
    //     $pelaporan->catatan_evaluasi = $request->post('catatan_evaluasi');
    //     $pelaporan->saran_rekomendasi = $request->post('saran_rekomendasi');
    //     $pelaporan->lampiran = $lampiranName;
    //     $pelaporan->id_pengawas = Auth::user()->id;
    //     $pelaporan->save();
    //     // dd($pelaporan->id);
    //     $this->buildUmpanBalik($pelaporan->id);
    //     // $this->buildUmpanBalik(1);

    //     return redirect()->route('pengawas.pelaporan')->with('success', 'Pelaporan berhasil disimpan!');
    // }
    public function save(Request $request)
    {
        // Mulai transaksi
        DB::beginTransaction();

        try {
            // Memproses lampiran jika ada
            $lampiranName = null;
            if ($request->hasFile('lampiran')) {
                $lampiran = $request->file('lampiran');

                // Generate a unique name based on the current date and time.
                $lampiranName = 'lampiran' . now()->format('YmdHis') . '_' . Str::random(10) . '.' . $lampiran->getClientOriginalExtension();

                // Store the image in the "lampiran" directory within the "public" disk.
                $request->lampiran->storeAs('lampiran', $lampiranName, 'public');
            }

            // Membuat instance Pelaporan baru
            $pelaporan = new Pelaporan();
            $pelaporan->kategori = $request->post('kategoriprogram_id');
            $pelaporan->sub_kategori = $request->post('sub_kategori');
            $pelaporan->sasaran = $request->post('sasaran');
            $pelaporan->object = $request->post('object_sasaran');
            $pelaporan->tgl_pendampingan = $request->post('tgl_pendampingan');
            $pelaporan->judul = $request->post('judul');
            $pelaporan->catatan_evaluasi = $request->post('catatan_evaluasi');
            $pelaporan->saran_rekomendasi = $request->post('saran_rekomendasi');
            $pelaporan->lampiran = $lampiranName;
            $pelaporan->id_pengawas = Auth::user()->id;

            // Simpan pelaporan
            $pelaporan->save();

            // Panggil buildUmpanBalik setelah menyimpan pelaporan
            $this->buildUmpanBalik($pelaporan->id);

            // Commit transaksi jika tidak ada kesalahan
            DB::commit();

            return redirect()->route('pengawas.pelaporan')->with('success', 'Pelaporan berhasil disimpan!');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Log error atau lakukan sesuatu dengan $e
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pelaporan: ' . $e->getMessage());
        }
    }


    // build buildUmpanBalik
    public static function buildUmpanBalik($id)
    {
        $pelaporan = Pelaporan::find($id);
        // $token = env('WABLAS_TOKEN');
        $token = 'OZ9q0PSQUUV4PRZGxyKUfZjt9EFyt22dTIRnklQSepTmFlrFMN9BqaIs7RXtnD9I';
        if ($pelaporan->sasaran == 'Guru') {
            $user = GuruM::find($pelaporan->object);
            
            
            

            $checkUmpanBalik = UmpanbalikT::where('id_user',$user->id)
                                ->where('id_pelaporan',$id)
                                ->where('id_pengawas',$pelaporan->id_pengawas)
                                ->first();
            if($checkUmpanBalik){
                $umpanBalik = $checkUmpanBalik;
                $umpanBalik->id_updated_by = Auth::user()->id;
                $umpanBalik->save();

            }else{
                $uniqueUrl = Str::uuid()->getHex();
                $umpanBalik = new UmpanbalikT();
                $umpanBalik->generate_url = $uniqueUrl;
            }

            $umpanBalik->id_pelaporan = $id;
            $umpanBalik->id_user = $user->id;
            $umpanBalik->id_pengawas = $pelaporan->id_pengawas;
            $umpanBalik->id_created_by = Auth::user()->id;

            $umpanBalik->save();
        
            // Kirim WA menggunakan curl

            $phone = $user->no_telp;
            $fullUrl = url('umpan-balik/' . $uniqueUrl);
            // yth. Bapak <<nama kepsek>>. Kepala <<nama sekolah>>. Pada bulan <<bulan>> <<tahun>> pengawas << nama pengawas>> akan melakukan kegiatan pendampingan <<nama rencana kerja>>  ke sekolah. Mohon dapat mengisi formulir Monev pada link berikut : 

            //     <<link monev>> 
                
            //     Berikut ini beberapa catatan yang penting : 
            //     1. pastikan link diisi pada hari pengawas melakukan pendampingan. 
            //     2. sertakan 1 bukti pendampingan berupa foto kegiatan bersama pengawas. 
                
            //     Terimakasih
                
            //     Pesan ini digenerate otomatis oleh Sistem Monitoring dan Evaluasi Digital Pengawas (DelmanSuper) KCD Kabupaten Tangerang

            $pesan = 'Yth Bapak / Ibu ' . $user->nama . ' Kepala <<nama sekolah>>, 
Pada bulan <<bulan>> <<tahun>> pengawas << nama pengawas>> akan melakukan kegiatan pendampingan <<nama rencana kerja>>  ke sekolah. 
Mohon dapat mengisi formulir Monev pada link berikut :
    
    ' . $fullUrl . '. 
  
    Berikut ini beberapa catatan yang penting : 
        1. pastikan link diisi pada hari pengawas melakukan pendampingan. 
        2. sertakan 1 bukti pendampingan berupa foto kegiatan bersama pengawas. 
                
    Terimakasih
                
Pesan ini digenerate otomatis oleh Sistem Monitoring dan Evaluasi Digital Pengawas (DelmanSuper) KCD Kabupaten Tangerang';
        
            // Inisialisasi curl
            $curl = curl_init();
        
            // Data yang akan dikirim
            $data = [
                'phone' => $phone,
                'message' => $pesan,
            ];
        
            // Set opsi curl
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: $token",
            ]);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, "https://jogja.wablas.com/api/send-message");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
            // Eksekusi curl dan dapatkan hasilnya
            $result = curl_exec($curl);
        
            // Tutup curl
            curl_close($curl);
        
            // Lihat hasil
            echo "<pre>";
            print_r($result);
        }
         else if ($pelaporan->sasaran = 'Kepala Sekolah') {
            $user = GuruM::find($pelaporan->object);

            // dd($user);
            $uniqueUrl = Str::uuid()->getHex();
            $umpanBalik = new UmpanbalikT();
            $umpanBalik->id_pelaporan = $id;
            $umpanBalik->id_user = $user->id;
            $umpanBalik->id_user = 1;
            $umpanBalik->id_pengawas = $pelaporan->id_pengawas;
            $umpanBalik->generate_url = $uniqueUrl;
            $umpanBalik->save();

            // Kirim WA menggunakan curl
          
            $phone = $user->no_telp;
            $fullUrl = url('umpan-balik/' . $uniqueUrl);
            $pesan = 'Yth Bapak / Ibu ' . $user->nama . ', 
Pengawas Pembina ' . Auth::user()->name . ' baru saja menyelesaikan kunjungan ke sekolah Bapak/Ibu. 
Mohon berkenan meluangkan Waktu untuk memberikan umpan balik terhadap kunjungan beliau melalui link berikut: 
' . $fullUrl . '. 
Terimakasih, Salam, 
Admin Delman Super';
        
            // Inisialisasi curl
            $curl = curl_init();
        
            // Data yang akan dikirim
            $data = [
                'phone' => $phone,
                'message' => $pesan,
            ];
        
            // Set opsi curl
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: $token",
            ]);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, "https://jogja.wablas.com/api/send-message");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
            // Eksekusi curl dan dapatkan hasilnya
            $result = curl_exec($curl);
        
            // Tutup curl
            curl_close($curl);
        
            // Lihat hasil
            echo "<pre>";
            print_r($result);
        } else {
            //sekolah
            $sekolah = SekolahM::with('gurukepalaSekolah')->find($pelaporan->object);

            // Access the related GuruM records
            foreach ($sekolah->gurukepalaSekolah as $user) {
                $uniqueUrl = Str::uuid()->getHex();
                $umpanBalik = new UmpanbalikT();
                $umpanBalik->id_pelaporan = $id;
                $umpanBalik->id_user = $user->id;
                $umpanBalik->id_pengawas = $pelaporan->id_pengawas;
                $umpanBalik->generate_url = $uniqueUrl;
                $umpanBalik->save();
                // dd($user);

                 // Kirim WA menggunakan curl
              
                $phone = $user->no_telp;
                $fullUrl = url('umpan-balik/' . $uniqueUrl);
            $pesan = 'Yth Bapak / Ibu ' . $user->nama . ', 
Pengawas Pembina ' . Auth::user()->name . ' baru saja menyelesaikan kunjungan ke sekolah Bapak/Ibu. 
Mohon berkenan meluangkan Waktu untuk memberikan umpan balik terhadap kunjungan beliau melalui link berikut: 
' . $fullUrl . '. 
Terimakasih, Salam, 
Admin Delman Super';
        
                // Inisialisasi curl
                $curl = curl_init();
            
                // Data yang akan dikirim
                $data = [
                    'phone' => $phone,
                    'message' => $pesan,
                ];
            
                // Set opsi curl
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    "Authorization: $token",
                ]);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, "https://jogja.wablas.com/api/send-message");
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            
                // Eksekusi curl dan dapatkan hasilnya
                $result = curl_exec($curl);
            
                // Tutup curl
                curl_close($curl);
            
                // Lihat hasil
                echo "<pre>";
                print_r($result);
            }
        }



        return true;
    }

    // get data pelaporan
    public function getdata(Request $request)
    {
        if ($request->ajax()) {


            $post = Pelaporan::with('kategoriprogram')
                ->where('id_pengawas', Auth::user()->id)->latest()->get();

            return Datatables::of($post)
                ->addIndexColumn()
                //         ->addColumn('tanggal', function($row){
                //             if ($row->tgl_pendampingan) {
                //                 $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $row->tgl_pendampingan);
                //                 return $carbonDate->format('d M Y H:i:s');
                //             } else {
                //                 return '-';
                //             }
                //    })
                ->addColumn('nama_kategori', function ($row) {
                    return $row->kategoriprogram->nama;
                })

                ->addColumn('nama_sekolah', function ($row) {
                    $sekolahIds = explode(',', $row->sekolah_id);
                    $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();

                    $nama_sekolah = '';
                    foreach ($sekolahs as $sekolah) {
                        $nama_sekolah .= '<span class="badge bg-label-primary m-1">' . $sekolah->nama_sekolah . '</span> '; // Added a space for separation
                    }
                    // dd($nama_sekolah);

                    return $nama_sekolah;
                })

                ->addColumn('action', function ($row) {

                    $btn = '<a  onclick="lihatPerencanaan(' . $row->id . ')" class="btn btn-sm bg-warning text-white " > <i class="fa fa-edit"></i> Edit</a>';
                    $btn = $btn . '<a href="#" onclick="addLampiran(' . $row->id . ')" class="btn btn-info btn-sm "><i class="fa fa-add"></i> Lampiran</a>';

                    return $btn;
                })
                ->rawColumns(['action', 'nama_kategori', 'nama_sekolah'])
                ->make(true);
        }
    }

    public function edit($id)
    {
        // Ambil data dari model berdasarkan ID atau yang lain sesuai kebutuhan
        $data = Pelaporan::findOrFail($id); // Gantilah YourModel dengan model yang sesuai

        return response()->json($data);
    }

    public function simpansubkategory(Request $request)
    {
        $subkategory = new SubKategory();
        $subkategory->id_kategory = $request->id_kategory;
        $subkategory->nama = $request->judul;
        $subkategory->save();

        return redirect()->route('pengawas.pelaporan')->with('success', 'sub kategori berhasil disimpan!');
    }

    public function getSubcategories(Request $request)
    {
        $kategoriId = $request->input('kategori_id');
        $kategori = Kategory::find($kategoriId);
        $explode = explode(" ", $kategori->nama);
        $sub = "Program " . $explode['1'];
        $kategorikerja = Kategory::where('nama', $sub)->first();
        $rencana = RencanaKerjaT::where('id_pengawas', Auth::user()->id)
            ->where("kategoriprogram_id", $kategorikerja->id)->get();



        $subcategories = [];
        foreach ($rencana as $key => $value) {
            $subcategories[$value->id] = $value->nama_program_kerja;
        }
        // $subcategories = SubKategory::where('id_kategory', $kategoriId)->get();
        // dd($subcategories);
        return response()->json(['subcategories' => $subcategories]);
    }

    public function getProgramKerja(Request $request)
    {
        $id = $request->input('id');
        $rencana = RencanaKerjaT::find($id);
        return response()->json(['rencana' => $rencana]);
    }

    public function getProgramKerjaSasaran(Request $request)
    {
        $id = $request->input('program');
        $sasaran = $request->input('sasaran');
        $rencana = RencanaKerjaT::find($id);
        $sekolahIds = explode(',', $rencana->sekolah_id);




        $objek = [];

        if ($sasaran == 'Sekolah') {
            $sekolahs = SekolahM::whereIn('id', $sekolahIds)->get();

            foreach ($sekolahs as $value) {
                $objek[$value->id] = $value->nama_sekolah;
            }
        } else if ($sasaran == 'Guru') {
            $guru = SekolahM::with('guru')->whereIn('id', $sekolahIds)->get();
            foreach ($guru as $value) {
                foreach ($value->guru as $guruItem) {
                    // Accessing the id of each GuruM related to the current SekolahM
                    $objek[$guruItem->id] = $guruItem->nama . ' - ' . $value->nama_sekolah;
                }
            }
        } else {
            $kepala = SekolahM::with('kepalaSekolah')->whereIn('id', $sekolahIds)->get();
            foreach ($kepala as $value) {
                foreach ($value->kepalaSekolah as $kepalaSekolahItem) {
                    // Accessing the id of each GuruM related to the current SekolahM
                    $objek[$kepalaSekolahItem->id] = $kepalaSekolahItem->nama . ' - ' . $value->nama_sekolah;
                }
            }
        }
        // dd($objek);
        return response()->json(['objek' => $objek]);
    }
}
