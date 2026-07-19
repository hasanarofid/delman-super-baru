<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonevBosp;
use Illuminate\Support\Facades\Auth;

class DashboardMonevBospController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');

        $user = Auth::user();
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten'])
                 ->whereHas('sekolah', function($q) {
                     $q->where('nama_sekolah', 'like', '%SMK%');
                 });

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten;
            $kabupaten_ids = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
            $query->whereHas('sekolah', function($q) use ($kabupaten_ids) {
                $q->whereIn('kabupaten_id', $kabupaten_ids);
            });
        }

        if ($user && strtolower($user->role) == 'pengawas') {
            $query->where('pengawas_id', $user->id);
        }

        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        // Calculate aggregated metrics for global dashboard
        $totalSekolahDimonev = $monevList->unique('sekolah_id')->count();
        $totalSiswaRiil = $monevList->sum('total_siswa_riil');
        
        $sekolahSelisihLebih = $monevList->filter(function($item) {
            return $item->total_siswa_riil > $item->siswa_dinas_bos;
        })->sum(function($item) {
            return $item->total_siswa_riil - $item->siswa_dinas_bos;
        });
        
        $sekolahSelisihKurang = $monevList->filter(function($item) {
            return $item->total_siswa_riil < $item->siswa_dinas_bos;
        })->sum(function($item) {
            return $item->siswa_dinas_bos - $item->total_siswa_riil;
        });

        $totalRealisasiBosp = $monevList->sum('realisasi_bosp');
        
        $statusIjopData = $monevList->groupBy('status_ijop')->map(function ($row) {
            return $row->count();
        })->toArray();

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('adminNew.dashboard_monev_bosp', compact(
            'monevList', 'year', 'month', 'bulanOptions', 
            'totalSekolahDimonev', 'totalSiswaRiil', 'sekolahSelisihLebih', 'sekolahSelisihKurang',
            'totalRealisasiBosp', 'statusIjopData'
        ));
    }
}
