<?php

namespace App\Http\Controllers;

use App\GuruM;
use App\Models\RencanaKerjaT;
use App\Models\UmpanbalikT;
use App\SekolahM;
use App\TanggapanUmpanbalikT;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\Traits\StakeholderAccess;

class SaranperbaikanController extends Controller
{
    use StakeholderAccess;
    public function index(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');
        $queryPengawas = $this->applyStakeholderFilter($queryPengawas, 'kabupaten_id', null, 'self');
        
        $listPengawas = $queryPengawas->get();

        return view('saranperbaikan.index',compact('listPengawas'));
    }

    public function indexpengawas(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');

        if (!$user) {
            return redirect()->route('login');
        }

        $queryPengawas = $this->applyStakeholderFilter($queryPengawas, 'kabupaten_id', null, 'self');
        
        $listPengawas = $queryPengawas->get();

        return view('dashboard_pengawas.umpanbalik.saranperbaikan',compact('listPengawas'));
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            $pengawas = $request->input('pengawas', 'all');

            $post = TanggapanUmpanbalikT::with('umpanBalikT')->latest();

            $post = $this->applyStakeholderFilter($post, 'umpanBalikT.pengawasnama.kabupaten_id', null, 'umpanBalikT.pengawasnama');
          
            $post->whereHas('umpanBalikT', function ($q) use ($pengawas) {
                if ($pengawas !== 'all') {
                    $q->where('id_pengawas', $pengawas);
                }
            });
       
               return Datatables::of($post->get())
                       ->addIndexColumn()
               
                    ->addColumn('nama_sekolah', function($row) {
                        $cariguru = GuruM::findorFail($row->umpanBalikT->id_user);
                        $sekolahs = SekolahM::findorFail($cariguru->sekolah_id);
                        return $sekolahs->nama_sekolah;
                    })
   
                    ->addColumn('saran_perbaikan', function($row){
                        return !empty($row->jawaban_10) ? $row->jawaban_10 : '-';
                    })

                    ->addColumn('pengawas', function($row){
                        return !empty($row->umpanBalikT->pengawasnama) ? $row->umpanBalikT->pengawasnama->name : '-';
                    })

                       ->rawColumns(['nama_sekolah','saran_perbaikan', 'pengawas'])
                       ->make(true);
           }
    }

    public function getdatapengawas(Request $request){
        if ($request->ajax()) {

            $pengawas = $request->input('pengawas', 'all');

            $post = TanggapanUmpanbalikT::with('umpanBalikT')
            ->whereHas('umpanBalikT', function ($query) {
                $query->where('id_pengawas', Auth::user()->id);
            })
            ->latest();
          
            $post->whereHas('umpanBalikT', function ($q) use ($pengawas) {
                if ($pengawas !== 'all') {
                    $q->where('id_pengawas', $pengawas);
                }
            });
       
               return Datatables::of($post->get())
                       ->addIndexColumn()
               
                    ->addColumn('nama_sekolah', function($row) {
                        $cariguru = GuruM::findorFail($row->umpanBalikT->id_user);
                        $sekolahs = SekolahM::findorFail($cariguru->sekolah_id);
                        return $sekolahs->nama_sekolah;
                    })
   
                    ->addColumn('saran_perbaikan', function($row){
                        return !empty($row->jawaban_10) ? $row->jawaban_10 : '-';
                    })

                    ->addColumn('pengawas', function($row){
                        return !empty($row->umpanBalikT->pengawasnama) ? $row->umpanBalikT->pengawasnama->name : '-';
                    })

                       ->rawColumns(['nama_sekolah','saran_perbaikan', 'pengawas'])
                       ->make(true);
           }
    }
}
