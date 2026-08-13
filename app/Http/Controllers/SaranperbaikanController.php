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

    private function getMonthYearData()
    {
        $currentYear = date('Y');
        $years = range($currentYear - 5, $currentYear + 5);
        $monthNamesIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = [
                'value' => $i,
                'name' => $monthNamesIndo[$i]
            ];
        }

        return compact('months', 'currentYear', 'years');
    }

    public function index(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');
        $queryPengawas = $this->applyStakeholderFilter($queryPengawas, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah');
        $listPengawas = $queryPengawas->get();
        $monthYearData = $this->getMonthYearData();

        return view('saranperbaikan.index', array_merge(compact('listPengawas'), $monthYearData));
    }

    public function indexpengawas(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');

        if (!$user) {
            return redirect()->route('login');
        }

        $queryPengawas = $this->applyStakeholderFilter($queryPengawas, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah');
        $listPengawas = $queryPengawas->get();
        $monthYearData = $this->getMonthYearData();

        return view('dashboard_pengawas.umpanbalik.saranperbaikan', array_merge(compact('listPengawas'), $monthYearData));
    }

    private function applyMonthYearFilters($query, Request $request)
    {
        $bln = $request->input('bln', 'all');
        $tahun = $request->input('tahun', 'all');

        $monthNamesIndoMap = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        if ($bln !== 'all') {
            $mNum = $monthNamesIndoMap[$bln] ?? null;
            $query->whereHas('umpanBalikT', function ($q) use ($bln, $mNum) {
                $q->where(function($sq) use ($bln, $mNum) {
                    $sq->whereHas('rencanakerja', function($rq) use ($bln) {
                        $rq->where('bulan', $bln);
                    });
                    if ($mNum) {
                        $sq->orWhereMonth('created_at', $mNum)
                          ->orWhereMonth('submitted_at', $mNum);
                    }
                });
            });
        }

        if ($tahun !== 'all') {
            $query->whereHas('umpanBalikT', function ($q) use ($tahun) {
                $q->where(function($sq) use ($tahun) {
                    $sq->whereHas('rencanakerja', function($rq) use ($tahun) {
                        $rq->where('tahun_ajaran', $tahun);
                    })->orWhereYear('created_at', $tahun)
                      ->orWhereYear('submitted_at', $tahun);
                });
            });
        }

        return $query;
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

            $post = $this->applyMonthYearFilters($post, $request);
       
            return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('nama_sekolah', function($row) {
                    $cariguru = GuruM::find($row->umpanBalikT->id_user ?? 0);
                    if (!$cariguru) return '-';
                    $sekolahs = SekolahM::find($cariguru->sekolah_id);
                    return $sekolahs ? $sekolahs->nama_sekolah : '-';
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

            $post = $this->applyMonthYearFilters($post, $request);
       
            return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('nama_sekolah', function($row) {
                    $cariguru = GuruM::find($row->umpanBalikT->id_user ?? 0);
                    if (!$cariguru) return '-';
                    $sekolahs = SekolahM::find($cariguru->sekolah_id);
                    return $sekolahs ? $sekolahs->nama_sekolah : '-';
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
