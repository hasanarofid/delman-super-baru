<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use App\GuruM;
use App\SekolahM;
use App\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Auth;
class StakeholderController extends Controller
{
    public function index()
    {
      
        return view('stakeholder.index');
    }

    /** get data */
    public function getdata(Request $request){
        if ($request->ajax()) {
            if(Auth::user()->role == 'Super Admin'){
                $post = User::with('kabupaten')->where('role','Stakeholder')->latest()->get();

            }else if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Stakeholder' ){
                $kelompok_kabupaten = Kabupaten::find(Auth::user()->kabupaten_id)->kelompok_kabupaten;
                $kabupaten = Kabupaten::where('kelompok_kabupaten',$kelompok_kabupaten)->get();
                $id_filter = [];
                foreach($kabupaten as $kab){
                    $id_filter[] = $kab->id;
                }
    
                $post = User::with('kabupaten')->where('role','Stakeholder')->whereIn('kabupaten_id',$id_filter)->latest()->get();
    
            }

            // dd($post);
            return Datatables::of($post)
                    ->addIndexColumn()
                     ->addColumn('foto', function($row){
                        if($row->foto_profile == 'userdefault.jpg'){
                            $foto = asset('userdefault.jpg');
                        }else{
                            $foto =  route('stakeholder',$row->foto_profile );
                        }

                     return  '<div class="card card-profile"><img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top"></div>';
                    })->addColumn('no_telp', function($row){
                        return !empty($row->no_telp) ? $row->no_telp: '-';
             })
                      ->addColumn('alamat', function($row){
                               return !empty($row->alamat_lengkap) ? $row->alamat_lengkap: '-';
                    })
                      ->addColumn('kabupaten', function($row){
                        return !empty($row->kabupaten->kelompok_kabupaten) ? $row->kabupaten->kelompok_kabupaten : '-';
                    })
                    ->addColumn('action', function($row){
                           if(Auth::user()->role == 'Stakeholder'){
                               return '-';
                           }
                           $btn = '<a href="'.route('stakeholder.edit',$row->id).'" data-toggle="tooltip"  class="edit btn btn-primary btn-sm editPost">Edit</a>';
                           $btn = $btn.' <a href="'.route('stakeholder.hapus',$row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal"    data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                            return $btn;
                    })
                    ->rawColumns(['no_telp','alamat','action','foto','kabupaten'])
                    ->make(true);
        }
        return view('stakeholder.data');
    }
    

    public function add(){
        $kelompok_kabupaten = Kabupaten::find(Auth::user()->kabupaten_id)->kelompok_kabupaten;
               
        $wilayah = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'),
         DB::raw('COUNT(*) as total'))
        ->groupBy('nama_kabupaten')
        ->where('kelompok_kabupaten',$kelompok_kabupaten)
        ->get();
    
        // dd($wilayah);
         return view('stakeholder.add',compact('wilayah'));
    }

    public function edit($id){
        // $model = User::find($id);
        // $wilayah = Kabupaten::select('kelompok_kabupaten', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as total'))
        //      ->groupBy('kelompok_kabupaten')
        //      ->get();
    
        // dd($wilayah);
        $models = User::where('id',$id)->first();

        $kelompok_kabupaten = Kabupaten::find(Auth::user()->kabupaten_id)->kelompok_kabupaten;
               
        $wilayah = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'),
         DB::raw('COUNT(*) as total'))
        ->groupBy('nama_kabupaten')
        ->where('kelompok_kabupaten',$kelompok_kabupaten)
        ->get();

        return view('stakeholder.edit',compact('models','wilayah'));
    }

     /** save data admin */
    public function store(Request $request){
        // dd($request->post());die;
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
            $user->pangkat = $request->pangkat;
            $user->gol_ruang = $request->gol_ruang;
            $user->foto_profile = 'userdefault.jpg';
            $user->role = 'Stakeholder';
            $user->kabupaten_id =  $request->kabupaten_id;
            $user->password = Hash::make($request->password);
            $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
            $user->save();

            return redirect()->route('stakeholder.index')->with('success', 'stakeholder created successfully');
    }

    /** save data admin */
    public function update($id,Request $request){
        // dd($id);
        // dd($request->post());die;
            //  $request->validate([
            //     'name' => 'required|string|max:255',
            //     'email' => 'required|email|unique:users',
            //     'password' => 'required|string|min:6',
            // ]);
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->kabupaten_id =  $request->kabupaten_id;
         
            $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
            $user->save();

             if(isset($request->password)){
            $user->password = Hash::make($request->password);
            $user->update();
        }   

            return redirect()->route('stakeholder.index')->with('success', 'stakeholder updated successfully');
    }

    public function hapus($id){
         $user = User::where('id',$id)->delete();
        return redirect()->back()->with('success', 'stakeholder Delete successfully');
    }
}
