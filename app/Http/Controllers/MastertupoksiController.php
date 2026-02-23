<?php

namespace App\Http\Controllers;

use App\Models\Kategory;
use Illuminate\Http\Request;
use App\MasterTupoksi;
use App\Kabupaten;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Imports\ImportUser;
use App\Exports\ExportUser;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Auth;

class MastertupoksiController extends Controller
{
    //index
    public function index(){
        return view('mastertupoksi.index');
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
          
                $post = Kategory::where('type', 'pelaporan')->get();
    
            // dd($post);
            return Datatables::of($post)
            ->addIndexColumn()
                    ->addColumn('status', function($row){
                        $badge = $row->status == 'active' ? 'bg-success' : 'bg-danger';
                        return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                    })
                    ->addColumn('action', function($row){
   
                        $user = Auth::user();
                        if ($user && $user->role == 'Super Admin') {
                            $btn = '<a href="'.route('mastertupoksi.edit', $row->id).'" data-toggle="tooltip" class="edit btn btn-primary btn-sm editPost">Edit</a>';
                            $btn .= ' <a href="'.route('mastertupoksi.hapus', $row->id).'" data-toggle="tooltip" data-toggle="modal" data-target="#confirmDeleteModal" data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                            return $btn;
                        } else {
                            return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                        }
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('mastertupoksi.index');
    }

    public function importfile(Request $request){
        Excel::import(new ImportUser,
                      $request->file('file')->store('files'));
        return redirect()->back()->with('success', 'pengawas Import successfully');
       
    }

    public function excelcontoh(Request $request){
         $models = User::where('role','Pengawas')->limit(1)->get();
        $judul = 'Contoh Data pengawas';
        return Excel::download(new ExportUser($models), $judul.'.xlsx');
    }

    /** add data pengawas */
    public function add(){
       
         return view('mastertupoksi.add');
    }

     /** add data pengawas */
    public function import(){
        return view('pengawas.import');
    }

    /** save data pengawas */
    public function store(Request $request){
        // dd($request->post());die;
  
            $model = new Kategory();
            $model->nama = $request->nama;
            $model->type = 'Pelaporan';
            $model->status = $request->status ?? 'active';
            $model->save();

        
            return redirect()->route('mastertupoksi.index')->with('success', 'Kategori Program created successfully');
    }

    public function edit($id){
        $models = Kategory::where('id',$id)->first();
        return view('mastertupoksi.edit',compact('models'));
    }

     public function hapus($id){
         $user = Kategory::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Kategori Program Delete successfully');
    }

    public function update(Request $request){
         $user = Kategory::where('id',$request->id)->first();
         $user->nama = $request->nama;
         $user->status = $request->status;
             $user->save();

        return redirect()->route('mastertupoksi.index',$request->id)->with('success', 'Kategori Program update successfully');
    }

    public function getkegiatan(Request $request)
    {
        $search = $request->term;
        $data = MasterTupoksi::select('kegiatan as text', 'id')
        ->whereNull('id_kegiatan')
        ->where('kegiatan', 'LIKE', "%$search%")
        ->get();
        
        return response()->json($data);
    }

}
