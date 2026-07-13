<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonevBulanan;
use Illuminate\Support\Facades\Auth;

class DashboardMonevBulananController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');

        $query = MonevBulanan::with(['pengawas', 'sekolah']);

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        $monevData = $query->get();

        // Calculate aggregated metrics
        $metrics = [
            'total_laporan' => $monevData->count(),
            'total_mou' => $monevData->sum('total_mou'),
            'total_prestasi' => $monevData->sum('jumlah_prestasi'),
            'avg_serapan_bosp' => $monevData->avg('serapan_bosp') ?? 0,
        ];

        // Prepare data for Chart.js
        $lulusanData = [
            'Bekerja' => $monevData->sum('lulusan_kerja'),
            'Melanjutkan Kuliah' => $monevData->sum('lulusan_kuliah'),
            'Wirausaha' => $monevData->sum('lulusan_wirausaha')
        ];

        $dinamikaSiswaData = [
            'Drop Out' => $monevData->sum('siswa_do'),
            'Mutasi' => $monevData->sum('siswa_mutasi'),
            'Pindahan' => $monevData->sum('siswa_pindahan')
        ];

        $mouData = [
            'Penyelarasan kurikulum' => $monevData->sum('mou_kurikulum'),
            'Peningkatan kompetensi guru' => $monevData->sum('mou_guru'),
            'Peningkatan kompetensi siswa (pkl)' => $monevData->sum('mou_murid'),
            'Sertifikasi (LSP atau Ujikom)' => $monevData->sum('mou_sertifikasi'),
            'Rekruitmen' => $monevData->sum('mou_rekrutmen'),
            'MoU Bantuan atau CSR' => $monevData->sum('mou_csr'),
        ];

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('adminNew.dashboard_monev', compact('metrics', 'lulusanData', 'dinamikaSiswaData', 'mouData', 'year', 'month', 'bulanOptions', 'monevData'));
    }
}
