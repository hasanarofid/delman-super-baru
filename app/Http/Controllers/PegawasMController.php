<?php

namespace App\Http\Controllers;
use App\Models\SekolahbinaanT;
use App\User;
use App\Profile;
use App\GuruM;
use App\SekolahM;
use App\Kabupaten;
use App\GolPangkatRuang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Imports\ImportUser;
use App\Exports\ExportUser;
use App\Exports\ExportPengawas;
use App\Exports\ExportDelmanSuper;
use App\Imports\ImportPengawas;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Facades\Http;

class PegawasMController extends Controller
{
    //tesWa
    // public function tesWa(){
    //     $token = env('WABLAS_TOKEN');
    //     $phone = '62881026697527'; // Ganti dengan nomor telepon tujuan
    //     $message = 'test get'; // Ganti dengan pesan Anda

    //     $response = Http::get("https://jogja.wablas.com/api/send-message", [
    //         'phone' => $phone,
    //         'message' => $message,
    //         'token' => $token,
    //     ]);

    //     $result = $response->body();

    //     echo "<pre>";
    //     dd($result);

    // }

    public function getpangkat(Request $request)
    {
        $search = $request->term;
        $id = $request->id;

        // Jika mencari berdasarkan ID/Nama yang sudah terpilih
        if ($id) {
            return response()->json(['id' => $id, 'text' => $id]);
        }

        $data = DB::table('table_gol_pangkat_ruang')
            ->whereNotNull('pangkat')
            ->where('pangkat', '<>', '')
            ->where('pangkat', '<>', '0')
            ->when($search, function($query) use ($search) {
                return $query->where('pangkat', 'LIKE', "%$search%");
            })
            ->distinct()
            ->pluck('pangkat');

        $results = $data->map(function($item) {
            return ['id' => $item, 'text' => $item];
        });

        return response()->json($results);
    }

    public function getRuang(Request $request)
    {
        $search = $request->term;
        $pangkat = $request->pangkat;
        $id = $request->id;

        // Jika mencari berdasarkan ID/Nama yang sudah terpilih
        if ($id) {
            return response()->json(['id' => $id, 'text' => $id]);
        }

        $data = DB::table('table_gol_pangkat_ruang')
            ->whereNotNull('ruang_kerja')
            ->where('ruang_kerja', '<>', '')
            ->where('ruang_kerja', '<>', '0')
            ->when($pangkat, function($query) use ($pangkat) {
                return $query->where('pangkat', $pangkat);
            })
            ->when($search, function($query) use ($search) {
                return $query->where('ruang_kerja', 'LIKE', "%$search%");
            })
            ->distinct()
            ->pluck('ruang_kerja');

        $results = $data->map(function($item) {
            return ['id' => $item, 'text' => $item];
        });

        return response()->json($results);
    }

    /**
     * menampilkan data pengawas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sekolah = SekolahM::where('kabupaten_id',Auth::user()->kabupaten_id)->get();
        // dd($sekolah);
        return view('pengawas.index',compact('sekolah'));
    }

    public function setSekolahBinaan($id){
        $models = User::where('id',$id)->first();
        $sekolah = SekolahM::get();
        $binaan = SekolahbinaanT::with('sekolah')->where('id_pengawas',$id)->get();
        $total_binaan = SekolahbinaanT::with('sekolah')->where('id_pengawas',$id)->count();

        // dd($binaan);
        return view('pengawas.add_sekolahbinaan',compact('models','sekolah','binaan','total_binaan'));
    }



    public function getdata(Request $request){
        if ($request->ajax()) {
            // if(Auth::user()->role == 'Super Admin'){
            //     $post = User::with('kabupaten')->where('role','Pengawas')->latest()->get(); 
            // }else if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Stakeholder' ){
            //     $kelompok_kabupaten = Kabupaten::find(Auth::user()->kabupaten_id)->kelompok_kabupaten;
            //     $kabupaten = Kabupaten::where('kelompok_kabupaten',$kelompok_kabupaten)->get();
            //     $id_filter = [];
            //     foreach($kabupaten as $kab){
            //         $id_filter[] = $kab->id;
            //     }
    
                $post = User::with('kabupaten', 'profile')
                ->where('role','Pengawas')
                // ->whereIn('kabupaten_id',$id_filter)
                ->latest()->get();
    
            // }
            // dd($post);
            return Datatables::of($post)
                    ->addIndexColumn()
                     ->addColumn('foto', function($row){
                        if($row->foto_profile == 'userdefault.jpg'){
                            $foto = asset('userdefault.jpg');
                        }else{
                            $foto = route('fotopengawas', $row->foto_profile);
                            // $foto =  route('pengawas',$row->foto_profile );
                        }

                     return  ' <div class="card card-profile"><img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top"></div>';
                    })->addColumn('no_telp', function($row){
                        return !empty($row->profile->no_telp) ? $row->profile->no_telp: '-';
             })
                      ->addColumn('alamat', function($row){
                               return !empty($row->profile->alamat_lengkap) ? $row->profile->alamat_lengkap: '-';
                    })
                      ->addColumn('kabupaten', function($row){
                        return !empty($row->kabupaten->nama_kabupaten) ? $row->kabupaten->nama_kabupaten: '-';
                    })
                    ->addColumn('binaan', function($row){
                        $binaan = SekolahbinaanT::with('sekolah')->where('id_pengawas',$row->id)->get();
                        $bin = '';
                        foreach ($binaan as $key => $value) {
                            
                            $bin .= '<span class="badge bg-label-primary m-1">'.$value->sekolah->nama_sekolah.'</span>';
                        }
                        return $bin; 

                    })
                    ->editColumn('pangkat', function($row){
                        return (!empty($row->pangkat) && $row->pangkat !== '0') ? $row->pangkat : '-';
                    })
                    ->editColumn('gol_ruang', function($row){
                        return (!empty($row->gol_ruang) && $row->gol_ruang !== '0') ? $row->gol_ruang : '-';
                    })
                    


                    ->addColumn('action', function($row){
                        $user = Auth::user();
                        if ($user && $user->role == 'Super Admin') {
                            $btn = '<a href="'.route('masterpengawas.edit',$row->id).'" data-toggle="tooltip"  class="edit btn btn-primary btn-sm editPost">Edit</a>';
                           $btn = $btn.' <a href="'.route('masterpengawas.hapus',$row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal"    data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                           $btn = $btn.' <a href="'.route('masterpengawas.setSekolahBinaan',$row->id).'"  class="btn btn-info btn-sm">Add Sekolah Binaan</a>';
    
                            return $btn;
                        } else {
                            return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                        }
                          
                    })
                    ->rawColumns(['no_telp','alamat','action','foto','kabupaten','binaan'])
                    ->make(true);
        }
        return view('pengawas.index');
    }

    public function importfile(Request $request){
        ini_set('max_execution_time', 3600); // 1 hour
        ini_set('memory_limit', '2048M');    // 2GB memory
        
        Excel::import(new ImportPengawas,
                      $request->file('file')->store('files'));
        return redirect()->back()->with('success', 'pengawas Import successfully');
       
    }

    public function excelcontoh(Request $request){
         $models = User::where('role','Pengawas')->limit(1)->get();
         if($models->isEmpty()){
            $dummy = new User();
            $dummy->name = 'Dr. Eko Supraptono, M.Si.';
            $dummy->nip = '196404151992031006';
            $dummy->jenjang_jabatan = 'Pengawas Sekolah Utama';
            $dummy->pangkat = 'Pembina Utama Madya';
            $dummy->gol_ruang = 'IV/d';
            $models = collect([$dummy]);
         }
        $judul = 'Contoh Data pengawas';
        return Excel::download(new ExportPengawas($models), $judul.'.xlsx');
    }

    public function export(Request $request){
        $models = User::where('role','Pengawas')->latest()->get();
        $judul = 'Data Pengawas ' . date('Y-m-d');
        return Excel::download(new ExportPengawas($models), $judul.'.xlsx');
    }

    public function exportDelmanSuper(Request $request){
        $models = User::where('role','Pengawas')->latest()->get();
        $judul = 'Export_Delman_Super_' . date('Y-m-d');
        return Excel::download(new ExportDelmanSuper($models), $judul.'.xlsx');
    }

    /** add data pengawas */
    public function add(){
        //  $wilayah = Kabupaten::get();
        // dd($wilayah);
         return view('pengawas.add');
    }

     /** add data pengawas */
    public function import(){
        return view('pengawas.import');
    }
    public function store_sekolah(Request $request)
    {
        $sekolahIds = $request->input('sekolah_id') ?? [];
        $id_pengawas = $request->input('id_pengawas');
    
        // Retrieve current sekolah binaan for the supervisor
        $currentSekolahIds = SekolahbinaanT::where('id_pengawas', $id_pengawas)->pluck('id_sekolah')->toArray();
    
        // Find sekolah_ids to delete (currently in the database but not in the new request)
        $deleteSekolahIds = array_diff($currentSekolahIds, $sekolahIds);
        
        // Find sekolah_ids to add (in the new request but not in the database)
        $addSekolahIds = array_diff($sekolahIds, $currentSekolahIds);
    
        // Delete removed sekolah binaan
        if (!empty($deleteSekolahIds)) {
            SekolahbinaanT::where('id_pengawas', $id_pengawas)
                ->whereIn('id_sekolah', $deleteSekolahIds)
                ->delete();
        }
    
        // Add new sekolah binaan
        foreach ($addSekolahIds as $sekolahId) {
            $skolahbinaan = new SekolahbinaanT();
            $skolahbinaan->id_pengawas = $id_pengawas;
            $skolahbinaan->id_sekolah = $sekolahId;
            $skolahbinaan->save();
        }
    
        return redirect()->back()
                         ->with('success', 'Sekolah binaan pengawas updated successfully');
    }
    /** save data pengawas */
    public function store(Request $request){
        $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->nip = $request->nip;
            $user->jenjang_jabatan = $request->jenjang_jabatan;
            $user->pangkat = ($request->pangkat !== "0") ? $request->pangkat : null;
            $user->gol_ruang = ($request->gol_ruang !== "0") ? $request->gol_ruang : null;
            $user->foto_profile = 'userdefault.jpg';

            $user->password = Hash::make($request->password);
            $user->role = 'Pengawas';
            
            $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
            $user->kabupaten_id = 1; // Set kabupaten_id to 1 as requested
            $user->save();

            // Update associated profile
            if ($user->profile) {
                $user->profile->update([
                    'no_telp' => $request->no_telp,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'kota' => $request->kota,
                    'kode_area' => $request->kode_area,
                ]);
            }

            return redirect()->route('masterpengawas.index')->with('success', 'pengawas created successfully');
    }

    public function edit($id){
        $models = User::where('id',$id)->first();
        return view('pengawas.edit',compact('models'));
    }

     public function hapus($id){
        $countSekolahBinaan = SekolahbinaanT::where('id_pengawas', $id)->count();

        if ($countSekolahBinaan > 0) {
            return redirect()->back()->with('error', 'Data tidak bisa dihapus karena pengawas masih memiliki ' . $countSekolahBinaan . ' sekolah binaan. Silakan hapus sekolah binaan terlebih dahulu.');
        }

        $user = User::where('id',$id)->delete();
        return redirect()->back()->with('success', 'pengawas Delete successfully');
    }

    public function update(Request $request){
         $user = User::where('id',$request->id)->first();
         $user->jenjang_jabatan = $request->jenjang_jabatan;
         $user->pangkat = ($request->pangkat !== "0") ? $request->pangkat : null;
         $user->gol_ruang = ($request->gol_ruang !== "0") ? $request->gol_ruang : null;
       
               $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
             $user->save();

            // Update associated profile
            if ($user->profile) {
                $user->profile->update([
                    'no_telp' => $request->no_telp,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'kota' => $request->kota,
                    'kode_area' => $request->kode_area,
                ]);
            } else {
                // If profile doesn't exist for some reason, create it
                $user->profile()->create([
                    'no_telp' => $request->no_telp,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'kota' => $request->kota,
                    'kode_area' => $request->kode_area,
                ]);
            }

        if(isset($request->password)){
            $user->password = Hash::make($request->password);
            $user->update();
        }

        return redirect()->route('masterpengawas.index',$request->id)->with('success', 'pengawas update successfully');
    }


    
}