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
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class LayanandibutuhkanController extends Controller
{
    public function index(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');

        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
            $queryPengawas->where('kabupaten_id', $user->kabupaten_id);
        }
        
        $listPengawas = $queryPengawas->get();

        return view('layanandibutuhkan.index',compact('listPengawas'));
    }

    public function indexpengawas(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
            $queryPengawas->where('kabupaten_id', $user->kabupaten_id);
        }
        
        $listPengawas = $queryPengawas->get();

        return view('dashboard_pengawas.umpanbalik.layanan',compact('listPengawas'));
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            $pengawas = $request->input('pengawas', 'all');

            $post = TanggapanUmpanbalikT::with('umpanBalikT')->latest();

            if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
                $post->whereHas('umpanBalikT.pengawasnama', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }
          
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
   
                    ->addColumn('layanan', function($row){
                        return !empty($row->jawaban_11) ? $row->jawaban_11 : '-';
                    })

                
   
        

                       ->rawColumns(['nama_sekolah','layanan'])
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
   
                    ->addColumn('layanan', function($row){
                        return !empty($row->jawaban_11) ? $row->jawaban_11 : '-';
                    })

                
   
        

                       ->rawColumns(['nama_sekolah','layanan'])
                       ->make(true);
           }
    }
    public function exportPDF(Request $request)
    {
        $pengawas = $request->input('pengawas', 'all');
        $search = $request->input('search', '');

        $query = TanggapanUmpanbalikT::with('umpanBalikT.pengawasnama', 'umpanBalikT.user.sekolah')
            ->latest();

        $userAuth = Auth::user();
        if ($userAuth->role == 'Stakeholder' || $userAuth->role == 'Admin') {
            $query->whereHas('umpanBalikT.pengawasnama', function($q) use ($userAuth) {
                $q->where('kabupaten_id', $userAuth->kabupaten_id);
            });
        }

        $query->whereHas('umpanBalikT', function ($q) use ($pengawas) {
            if ($pengawas !== 'all') {
                $q->where('id_pengawas', $pengawas);
            }
        });

        // Fetch supervisor profile if specific pengawas is selected
        $pengawasProfile = null;
        if ($pengawas !== 'all' && $pengawas != 0) {
            $pengawasProfile = User::with('profile')->find($pengawas);
        }

        $data = $query->get()->map(function ($row) {
            $namaSekolah = '-';
            try {
                $cariguru = GuruM::find($row->umpanBalikT->id_user);
                if ($cariguru) {
                    $sekolah = SekolahM::find($cariguru->sekolah_id);
                    $namaSekolah = $sekolah ? $sekolah->nama_sekolah : '-';
                }
            } catch (\Exception $e) {}

            return [
                'nama_sekolah' => $namaSekolah,
                'layanan' => $row->jawaban_11 ?? '-',
            ];
        });

        $pdf = PDF::loadView('layanandibutuhkan.layanan_pdf', [
            'data' => $data,
            'pengawasProfile' => $pengawasProfile,
            'generateDate' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Daftar_Kebutuhan_Layanan.pdf');
    }

    public function exportPDFPengawas(Request $request)
    {
        $userAuth = Auth::user();
        $query = TanggapanUmpanbalikT::with('umpanBalikT.pengawasnama', 'umpanBalikT.user.sekolah')
            ->whereHas('umpanBalikT', function ($q) use ($userAuth) {
                $q->where('id_pengawas', $userAuth->id);
            })
            ->latest();

        $pengawasProfile = User::with('profile')->find($userAuth->id);

        $data = $query->get()->map(function ($row) {
            $namaSekolah = '-';
            try {
                $cariguru = GuruM::find($row->umpanBalikT->id_user);
                if ($cariguru) {
                    $sekolah = SekolahM::find($cariguru->sekolah_id);
                    $namaSekolah = $sekolah ? $sekolah->nama_sekolah : '-';
                }
            } catch (\Exception $e) {}

            return [
                'nama_sekolah' => $namaSekolah,
                'layanan' => $row->jawaban_11 ?? '-',
            ];
        });

        $pdf = PDF::loadView('layanandibutuhkan.layanan_pdf', [
            'data' => $data,
            'pengawasProfile' => $pengawasProfile,
            'generateDate' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Daftar_Kebutuhan_Layanan.pdf');
    }
}
