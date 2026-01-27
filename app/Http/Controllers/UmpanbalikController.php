<?php

namespace App\Http\Controllers;

use App\Models\AspekProgram;
use App\Models\Pelaporan;
use App\Models\UmpanbalikM;
use App\Models\UmpanbalikT;
use App\Models\RencanaKerjaT;
use App\TanggapanUmpanbalikT;
use App\Models\UmpanbalikAnswer;
use App\Models\UmpanbalikCategory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
class UmpanbalikController extends Controller
{
   //index
   public function index(){
    return view('dashboard_pengawas.umpanbalik.index');
    }

    public function umpan($generate)
    {
        $model = UmpanbalikT::where('generate_url', $generate)->firstOrFail();
        
        // Periksa apakah umpan balik sudah disubmit
        $cektanggapan = TanggapanUmpanbalikT::where('id_umpanbalik', $model->id)->first();
        if ($cektanggapan || $model->submitted_at !== null) {
            return view('umpanbalik.done', compact('model'));
        }

        // Jika ada id_category dan bukan 0, gunakan dynamic form dari fitur baru
        if (!empty($model->id_category) && $model->id_category != 0) {
            $questions = UmpanbalikM::where('id_category', $model->id_category)->where('status', true)->orderBy('urutan')->get();
            $umpanbalikT = $model; // Define the alias variable
            return view('umpanbalik.dynamic_form', compact('model', 'questions', 'umpanbalikT')); 
        }

        // Jika id_category null atau 0, gunakan form lama (samakan dengan simodip)
        $pengawas = User::find($model->id_pengawas);
        $pelaporan = RencanaKerjaT::find($model->id_pelaporan);
        
        $umpanBalikM = UmpanbalikM::where('aspek', 'pendampingan')->orderBy('urutan')->get();
        $umpanBalikM2 = UmpanbalikM::where('aspek', 'kompetensi')->orderBy('urutan')->get();
        $umpanBalikM3 = UmpanbalikM::where('aspek', 'lainnya')->orderBy('urutan')->get();
        $asepek = AspekProgram::get();

        return view('umpanbalik.index', compact(
            'pengawas',
            'model',
            'umpanBalikM',
            'umpanBalikM2',
            'umpanBalikM3',
            'pelaporan',
            'asepek'
        ));
    }

    public function saveumpan(Request $request){

        // dd($request);
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');

            // Generate a unique name based on the current date and time.
            $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Store the image in the "blog" directory within the "shared" disk.
            $request->foto->storeAs('umpanbalik', $imageName, 'shared');

        }else{
            $imageName = 'userdefault.jpg';
        }
        $umpanbalik = UmpanbalikT::findOrFail($request->post('id_umpanbalik'));

        $model = new TanggapanUmpanbalikT();
        $model->id_umpanbalik  = $request->post('id_umpanbalik');
        $model->jawaban_1  = $request->post('jawaban_1');
        $model->jawaban_2  = $request->post('jawaban_2');
        $model->jawaban_3  = $request->post('jawaban_3');
        $model->jawaban_4  = $request->post('jawaban_4');
        $model->jawaban_5  = $request->post('jawaban_5');
        $model->jawaban_6  = $request->post('jawaban_6');
        $model->jawaban_7  = $request->post('jawaban_7');
        $model->jawaban_8  = $request->post('jawaban_8');
        $model->jawaban_9  = $request->post('jawaban_9');
        $model->jawaban_10  = $request->post('jawaban_10');
        $model->jawaban_11  = $request->post('jawaban_11');
        $model->tanggal_kedatangan  = $request->tgl_pendampingan;
        $model->id_user  = $umpanbalik->id_user;
        $model->foto  = $imageName;

        $model->save();

        // Update submitted_at in umpanbalik_t for consistency
        $umpanbalik->update(['submitted_at' => now()]);

        return redirect()->route('tanggapan')->with('success', 'Umpan Balik anda berhasil disimpan. terima kasih untuk tanggapan anda');
    }

    public function tanggapan(){
        return view('umpanbalik.umpanbalik');
    }

    public function getdata(Request $request){
        if ($request->ajax()) {


            $post = UmpanbalikT::where('id_pengawas',Auth::user()->id)->latest()->get();

               return Datatables::of($post)
                       ->addIndexColumn()
                       ->addColumn('tanggal', function($row){
                        return $row->created_at->format('d M Y h:i:s');
                    })
                    ->addColumn('sasaran', function($row){
                        $rencana = RencanaKerjaT::find($row->id_pelaporan);
                        return !empty($rencana) ? $rencana->nama_program_kerja : '-';
                    })

               ->addColumn('action', function($row){
                $fullUrl = url('umpan-balik-view/' . $row->generate_url);

                              $btn = '<a target="_blanck" href="'.$fullUrl.'"   class="btn btn-sm bg-warning text-white " > <i class="fa fa-view"></i> view</a>';

                               return $btn;
                       })
                       ->rawColumns(['action','sasaran','tanggal'])
                       ->make(true);
           }
    }

    public function umpanview($generate)
    {
        $model = UmpanbalikT::where('generate_url',$generate)->first();
        if (!$model) {
            abort(404);
        }

        // Jika ada id_category dan bukan 0, gunakan dynamic view dari fitur baru
        if (!empty($model->id_category) && $model->id_category != 0) {
            $umpanbalikT = UmpanbalikT::with(['category', 'user', 'pengawasnama', 'rencanakerja', 'answers.question'])
                ->where('generate_url', $generate)
                ->firstOrFail();

            $categoryName = $umpanbalikT->category ? $umpanbalikT->category->name : 'N/A';
            
            $questions = UmpanbalikM::where('id_category', $umpanbalikT->id_category)
                                    ->orderBy('urutan')
                                    ->get();

            return view('admin.umpanbalik.dynamic_view', compact('umpanbalikT', 'categoryName', 'questions'));
        }

        // Jika id_category null atau 0, gunakan logika lama (samakan dengan simodip)
        $pengawas = User::find($model->id_pengawas);
        $pelaporan = RencanaKerjaT::find($model->id_pelaporan);
        
        $umpanBalikM = UmpanbalikM::where('aspek','pendampingan')->orderBy('urutan')->get();
        $umpanBalikM2 = UmpanbalikM::where('aspek','kompetensi')->orderBy('urutan')->get();
        $umpanBalikM3 = UmpanbalikM::where('aspek','lainnya')->orderBy('urutan')->get();
        $asepek = AspekProgram::get();

        $tangapan = TanggapanUmpanbalikT::where('id_umpanbalik',$model->id)->first();
        
        return view('umpanbalik.view',compact(
            'pengawas',
            'model',
            'umpanBalikM',
            'umpanBalikM2',
            'umpanBalikM3',
            'pelaporan',
            'tangapan',
            'asepek'
        ));
    }




}
