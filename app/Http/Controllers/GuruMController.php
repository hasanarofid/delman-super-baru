<?php

namespace App\Http\Controllers;

use App\GuruM;
use App\SekolahM;
use App\Models\UmpanbalikT;
use Illuminate\Http\Request;
use DataTables;
use App\Imports\GuruImport;
use App\Exports\GuruExport;
use App\Exports\ExportKepalaSekolah;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Kabupaten;
use Auth;
use Exception;
class GuruMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return view('guru.index');
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
           
            // if(Auth::user()->role == 'Super Admin'){
            //     $post = GuruM::with('sekolah')->where('is_aktif',true)->latest()->get();
            // }else if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Stakeholder' ){
            //     $kelompok_kabupaten = Kabupaten::find(Auth::user()->kabupaten_id)->kelompok_kabupaten;
            //     $kabupaten = Kabupaten::where('kelompok_kabupaten',$kelompok_kabupaten)->get();
            //     $id_filter = [];
            //     foreach($kabupaten as $kab){
            //         $id_filter[] = $kab->id;
            //     }
    
                $post = GuruM::with('sekolah')->where('is_aktif',true)
                ->latest()->get();
    
            // }
            return Datatables::of($post)
                    ->addIndexColumn()
                     ->addColumn('nama_sekolah', function($row){
                               return !empty($row->sekolah->nama_sekolah) ? $row->sekolah->nama_sekolah: '-';
                    })
                    ->addColumn('action', function($row){
                        $user = Auth::user();
                        if ($user && $user->role == 'Super Admin') {
                            $btn = '<a href="'.route('guru.edit',$row->id).'" data-toggle="tooltip"  class="edit btn btn-primary btn-sm editPost">Edit</a>';
                            $btn = $btn.' <a href="'.route('guru.hapus',$row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal"    data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
     
                             return $btn;
                        } else {
                            return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                        }
                       
                 })
                    ->rawColumns(['nama_sekolah','action'])
                    ->make(true);
        }
        return view('guru.index');
    }


    public function importfile(Request $request)
    {
        try {
            // Import the file
            Excel::import(new GuruImport, $request->file('file')->store('files'));
    
            // If import is successful
            return redirect()->back()->with('success', 'Guru Import successfully');
            
        } catch (Exception $e) {
            // If there’s an error, redirect back with the error message
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
    

    public function excelcontoh(Request $request){
         $models = GuruM::with('sekolah')->where('is_aktif',true)->limit(1)->get();
         $judul = 'Contoh Data Guru';
        return Excel::download(new GuruExport($models), $judul.'.xlsx');
    }

    public function export(Request $request){
        $models = GuruM::with('sekolah')->where('is_aktif',true)->latest()->get();
        $judul = 'Data_Kepala_Sekolah_' . date('Y-m-d');
        return Excel::download(new ExportKepalaSekolah($models), $judul.'.xlsx');
    }

    /** add data Guru */
    public function add(){
        $listsekolah = SekolahM::where('is_aktif',true)->get();
      
        return view('guru.add',compact('listsekolah'));
    }

     /** add data Guru */
    public function import(){
        
        return view('guru.import');
    }

    /** save data Guru */
    public function store(Request $request){
        // $request->validate([
        //         'nama' => 'required|string|max:255'
        //                 ]);
            $guru = new GuruM();
            $guru->nama = $request->nama;
            $guru->no_telp = $request->no_telp;
            $guru->jabatan = $request->jabatan;
            $guru->kota = $request->kota;
            $guru->alamat_lengkap = $request->alamat_lengkap;
            $guru->kode_area = $request->kode_area;
            $guru->sekolah_id = $request->sekolah_id;
            $guru->kabupaten_id = 1;

            $guru->is_aktif = true;
            $guru->save();

            return redirect()->route('guru.add')->with('success', 'Guru created successfully');
    }

    public function edit($id){
        $models = GuruM::where('id',$id)->first();
        $listsekolah = SekolahM::where('is_aktif',true)->get();


        return view('guru.edit',compact('models','listsekolah'));
    }

     public function hapus($id){
        $countUmpanBalik = UmpanbalikT::where('id_user', $id)->count();

        if ($countUmpanBalik > 0) {
            return redirect()->back()->with('error', 'Data tidak bisa dihapus karena guru sudah memiliki ' . $countUmpanBalik . ' data umpan balik di rencana kerja.');
        }

         $user = GuruM::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Guru Delete successfully');
    }

    public function update(Request $request){
         $guru = GuruM::where('id',$request->id)->first();

         $guru->nama = $request->nama;
         $guru->no_telp = $request->no_telp;
         $guru->jabatan = $request->jabatan;
         $guru->kota = $request->kota;
         $guru->alamat_lengkap = $request->alamat_lengkap;
         $guru->kode_area = $request->kode_area;
         $guru->is_aktif = true;
         $guru->save();
           


        return redirect()->route('guru.edit',$request->id)->with('success', 'Guru update successfully');
    }
}
