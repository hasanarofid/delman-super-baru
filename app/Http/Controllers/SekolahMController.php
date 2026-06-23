<?php

namespace App\Http\Controllers;

use App\SekolahM;
use App\Models\RencanaKerjaT;
use Illuminate\Http\Request;
use DataTables;
use App\Imports\SekolahImport;
use App\Exports\SekolahExport;
use Illuminate\Support\Facades\DB;
use App\Kabupaten;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Auth;
use App\Traits\StakeholderAccess;

class SekolahMController extends Controller
{
    use StakeholderAccess;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          return view('sekolah.index');
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            $query = SekolahM::with(['kabupaten', 'kepalaSekolahSatu', 'pengawas.pengawas'])->where('is_aktif', true);

            $query = $this->applyStakeholderFilter($query, 'kabupaten_id', 'nama_sekolah', null);
    
            $post = $query->latest()->get();
    
            return Datatables::of($post)
                    ->addIndexColumn()
                    ->addColumn('kabupaten', function($row){
                        return !empty($row->kabupaten->nama_kabupaten) ? $row->kabupaten->nama_kabupaten: '-';
                    })
                    ->addColumn('kepasek', function($row){
                        return $row->kepalaSekolahSatu->nama_guru ?? '-';
                    })
                    ->addColumn('nama_pengawas', function($row){
                        return $row->pengawas->pengawas->name ?? '-';
                    })
                    ->addColumn('action', function($row){
                        $user = Auth::user();
                        if ($user && $user->role == 'Super Admin') {
                            $btn = '<a href="'.route('sekolah.edit',$row->id).'" data-toggle="tooltip"  class="edit btn btn-primary btn-sm editPost">Edit</a>';
                            $btn = $btn.' <a href="'.route('sekolah.hapus',$row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal"    data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                            $btn = $btn.' <button type="button" class="btn btn-warning btn-sm updateKabupatenBtn" data-id="'.$row->id.'" data-kabupaten-id="'.$row->kabupaten_id.'">Update Kabupaten</button>';
     
                             return $btn;
                        } else {
                            return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                        }
                       
                 })
                    ->rawColumns(['action','kabupaten', 'kepasek', 'nama_pengawas'])
                    ->make(true);
        }
        return view('sekolah.index');
    }

    public function importfile(Request $request){
        Excel::import(new SekolahImport,
                      $request->file('file')->store('files'));
        return redirect()->back()->with('success', 'Sekolah Import successfully');
       
    }

    public function excelcontoh(Request $request){
         $models = SekolahM::where('is_aktif',true)->limit(1)->get();
        $judul = 'Contoh Data Sekolah';
        return Excel::download(new SekolahExport($models), $judul.'.xlsx');
    }

    /** add data Sekolah */
    public function add(){
     
        return view('sekolah.add');
    }

     /** add data Sekolah */
    public function import(){
        return view('sekolah.import');
    }

    /** save data Sekolah */
    public function store(Request $request){
        // dd($request->post());die;
        $request->validate([
                'nama_sekolah' => 'required|string|max:255',
                'npsn' => 'required',
                Rule::unique('sekolah_m','npsn')
            ]);
            $sekolah = new SekolahM();
            $sekolah->nama_sekolah = $request->nama_sekolah;
            $sekolah->npsn = $request->npsn;
            $sekolah->no_telp = $request->no_telp;
            $sekolah->kota = $request->kota;
            $sekolah->alamat_lengkap = $request->alamat_lengkap;
            $sekolah->kode_area = $request->kode_area;
            $sekolah->kabupaten_id = 1;

            $sekolah->is_aktif = true;
            $sekolah->save();

            return redirect()->route('sekolah.add')->with('success', 'Sekolah created successfully');
    }

    public function edit($id){
        $models = SekolahM::where('id',$id)->first();
        return view('sekolah.edit',compact('models'));
    }

     public function hapus($id){
        $countRencanaKerja = RencanaKerjaT::where('sekolah_id', $id)->count();

        if ($countRencanaKerja > 0) {
            return redirect()->back()->with('error', 'Data tidak bisa dihapus karena sekolah sudah terdaftar di ' . $countRencanaKerja . ' rencana kerja.');
        }

         $user = SekolahM::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Sekolah Delete successfully');
    }

    public function update(Request $request){
         $sekolah = SekolahM::where('id',$request->id)->first();

         $sekolah->nama_sekolah = $request->nama_sekolah;
         $sekolah->npsn = $request->npsn;
         $sekolah->no_telp = $request->no_telp;
         $sekolah->kota = $request->kota;
         $sekolah->alamat_lengkap = $request->alamat_lengkap;
         $sekolah->kode_area = $request->kode_area;
         $sekolah->is_aktif = true;
         $sekolah->save();
           


        return redirect()->route('sekolah.edit',$request->id)->with('success', 'Sekolah update successfully');
    }

    public function updateKabupaten(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sekolah_m,id',
            'kabupaten_id' => 'required|exists:master_kabupaten,id',
        ]);

        $sekolah = SekolahM::find($request->id);
        $sekolah->kabupaten_id = $request->kabupaten_id;
        $sekolah->save();

        return response()->json(['success' => true, 'message' => 'Kabupaten Sekolah berhasil diupdate']);
    }
}
