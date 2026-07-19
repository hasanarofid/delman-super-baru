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
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten']);

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Models\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten;
            $kabupaten_ids = \App\Models\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
            $query->whereHas('sekolah', function($q) use ($kabupaten_ids) {
                $q->whereIn('kab_id', $kabupaten_ids);
            });
        }

        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        // Calculate aggregated metrics for global dashboard
        $totalSekolahDimonev = $monevList->unique('sekolah_id')->count();
        $totalSiswaRiil = $monevList->sum('total_siswa_riil');
        
        $sekolahSelisihLebih = $monevList->filter(function($item) {
            return $item->total_siswa_riil > $item->siswa_dinas_bos;
        })->count();
        
        $sekolahSelisihKurang = $monevList->filter(function($item) {
            return $item->total_siswa_riil < $item->siswa_dinas_bos;
        })->count();

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('adminNew.dashboard_monev_bosp', compact(
            'monevList', 'year', 'month', 'bulanOptions', 
            'totalSekolahDimonev', 'totalSiswaRiil', 'sekolahSelisihLebih', 'sekolahSelisihKurang'
        ));
    }
}
