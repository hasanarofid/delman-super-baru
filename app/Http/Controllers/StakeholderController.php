<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use App\GuruM;
use App\SekolahM;
use App\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class StakeholderController extends Controller
{
    public function index()
    {
      
        return view('stakeholder.index');
    }

    /** get data */
    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            
            if($user->role == 'Super Admin'){
                $post = User::with('kabupaten')->where('role','Stakeholder')->latest()->get();

            }else {
                // Admin atau Stakeholder
                $kabupaten_id = $user->kabupaten_id;
                if ($kabupaten_id) {
                    $kab = Kabupaten::find($kabupaten_id);
                    if ($kab) {
                        $kelompok_kabupaten = $kab->kelompok_kabupaten;
                        $kabupaten_ids = Kabupaten::where('kelompok_kabupaten',$kelompok_kabupaten)->pluck('id');
                        $post = User::with('kabupaten')->where('role','Stakeholder')->whereIn('kabupaten_id',$kabupaten_ids)->latest()->get();
                    } else {
                        $post = collect();
                    }
                } else {
                    $post = collect(); // Kosong jika tidak ada kabupaten_id
                }
            }

            // dd($post);
            return DataTables::of($post)
                    ->addIndexColumn()
                     ->addColumn('foto', function($row){
                        if($row->foto_profile == 'userdefault.jpg' || empty($row->foto_profile)){
                            $foto = asset('userdefault.jpg');
                        }else{
                            $foto =  route('fotopengawas',$row->foto_profile );
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
                           $user = Auth::user();
                           if($user->role == 'Stakeholder'){
                               return '-';
                           }
                           $btn = '<a href="'.route('stakeholder.edit',$row->id).'" data-toggle="tooltip"  class="edit btn btn-primary btn-sm editPost">Edit</a>';
                           $btn = $btn.' <a href="'.route('stakeholder.hapus',$row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal"    data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                           if ($user->role == 'Super Admin') {
                               $btn = $btn.' <button type="button" class="btn btn-warning btn-sm updateKabupatenBtn" data-id="'.$row->id.'" data-kabupaten-id="'.$row->kabupaten_id.'">Update Kabupaten</button>';
                           }
                            return $btn;
                    })
                    ->rawColumns(['no_telp','alamat','action','foto','kabupaten'])
                    ->make(true);
        }
    }
    

    public function add(){
        $user = Auth::user() ?: Auth::guard('stakeholder')->user();
               
        $query = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as total'))
            ->groupBy('nama_kabupaten');

        if($user->role != 'Super Admin' && $user->kabupaten_id){
            $kelompok_kabupaten = Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten;
            $query->where('kelompok_kabupaten', $kelompok_kabupaten);
        }
               
        $wilayah = $query->get();
    
         return view('stakeholder.add',compact('wilayah'));
    }

    public function edit($id){
        $models = User::where('id',$id)->first();
        $user = Auth::user() ?: Auth::guard('stakeholder')->user();
               
        $query = Kabupaten::select('nama_kabupaten', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as total'))
            ->groupBy('nama_kabupaten');

        if($user->role != 'Super Admin' && $user->kabupaten_id){
            $kelompok_kabupaten = Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten;
            $query->where('kelompok_kabupaten', $kelompok_kabupaten);
        }
               
        $wilayah = $query->get();

        return view('stakeholder.edit',compact('models','wilayah'));
    }

     /** save data admin */
    public function store(Request $request){
        // dd($request->post());die;
             $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
           'repeatpassword' => 'required|same:password',
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

    public function import(){
        return view('stakeholder.import');
    }

    public function importfile(Request $request){
        // Implementasi import jika diperlukan
        return redirect()->back()->with('success', 'Stakeholder Imported successfully');
    }

    public function excelcontoh(){
        // Implementasi download contoh excel jika diperlukan
        return redirect()->back();
    }

    public function updateKabupaten(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'kabupaten_id' => 'required|exists:master_kabupaten,id',
        ]);

        $user = User::find($request->id);
        $user->kabupaten_id = $request->kabupaten_id;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Kabupaten berhasil diupdate']);
    }
}
