<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonevBosp;
use Illuminate\Support\Facades\Auth;
use App\Traits\StakeholderAccess;

class DashboardMonevBospController extends Controller
{
    use StakeholderAccess;

    private function getFilteredMonevQuery(Request $request)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $user = Auth::user();
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten']);

        // Apply Stakeholder / Admin / Pengawas Access Control
        $query = $this->applyStakeholderFilter($query, 'sekolah.kabupaten_id', 'nama_sekolah', 'pengawas', 'sekolah');

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        if ($selectedKabupaten !== 'all') {
            $query->whereHas('sekolah', function($q) use ($selectedKabupaten) {
                $q->where('kabupaten_id', $selectedKabupaten);
            });
        }

        if ($user && strtolower($user->role) == 'pengawas') {
            $query->where('pengawas_id', $user->id);
        }

        return $query;
    }

    private function getKabupatenOptions($user)
    {
        if ($user && $user->role == 'Stakeholder') {
            $akses_kabupaten = json_decode($user->akses_kabupaten, true) ?? [];
            if (!empty($akses_kabupaten) && !in_array('All', $akses_kabupaten)) {
                return \App\Kabupaten::whereIn('id', $akses_kabupaten)->orderBy('nama_kabupaten', 'asc')->get();
            } elseif ($user->kabupaten_id) {
                $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
                if ($kelompok_kabupaten) {
                    return \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->orderBy('nama_kabupaten', 'asc')->get();
                } else {
                    return \App\Kabupaten::where('id', $user->kabupaten_id)->get();
                }
            }
        }
        return \App\Kabupaten::orderBy('nama_kabupaten', 'asc')->get();
    }

    private function getRekapKepatuhanData(Request $request, $user)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $pengawasQuery = \App\User::where('role', 'Pengawas');
        $pengawasQuery = $this->applyStakeholderFilter($pengawasQuery, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah');

        if ($selectedKabupaten !== 'all') {
            $pengawasQuery->where(function($pq) use ($selectedKabupaten) {
                $pq->where('kabupaten_id', $selectedKabupaten)
                   ->orWhereExists(function($q) use ($selectedKabupaten) {
                       $q->select(\Illuminate\Support\Facades\DB::raw(1))
                         ->from('sekolahbinaan_t')
                         ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                         ->whereRaw('sekolahbinaan_t.id_pengawas = users.id')
                         ->where('sekolah_m.kabupaten_id', $selectedKabupaten);
                   });
            });
        }

        $pengawasList = $pengawasQuery->get();

        $rekapKepatuhanPengawas = [];
        $totalPengawas = $pengawasList->count();
        $pengawasSudahLaporCount = 0;
        $totalSekolahBinaanWilayah = 0;

        foreach ($pengawasList as $p) {
            $binaanQueryP = \App\Models\SekolahbinaanT::where('id_pengawas', $p->id);
            if ($user && $user->role == 'Stakeholder') {
                $binaanQueryP = $this->applyStakeholderFilter($binaanQueryP, 'sekolah.kabupaten_id', 'nama_sekolah', null, 'sekolah');
            }

            if ($selectedKabupaten !== 'all') {
                $binaanQueryP->whereHas('sekolah', function($q) use ($selectedKabupaten) {
                    $q->where('kabupaten_id', $selectedKabupaten);
                });
            }

            $binaanIds = $binaanQueryP->pluck('id_sekolah')->toArray();
            $totalBinaanP = count($binaanIds);
            $totalSekolahBinaanWilayah += $totalBinaanP;

            $monevQueryP = MonevBosp::where('pengawas_id', $p->id);
            if ($year !== 'all') {
                $monevQueryP->where('tahun', $year);
            }
            if ($month !== 'all') {
                $monevQueryP->where('bulan', $month);
            }
            $sudahMonevIds = $monevQueryP->pluck('sekolah_id')->unique()->toArray();
            $sudahMonevCount = count(array_intersect($binaanIds, $sudahMonevIds));

            if ($sudahMonevCount > 0) {
                $pengawasSudahLaporCount++;
            }

            $pctP = $totalBinaanP > 0 ? round(($sudahMonevCount / $totalBinaanP) * 100, 1) : 0;

            if ($pctP >= 100) {
                $statusText = 'Selesai (100%)';
                $statusBadge = 'bg-success';
            } elseif ($sudahMonevCount > 0) {
                $statusText = 'Dalam Proses';
                $statusBadge = 'bg-warning text-dark';
            } else {
                $statusText = 'Belum Lapor';
                $statusBadge = 'bg-danger';
            }

            $rekapKepatuhanPengawas[] = [
                'nama' => $p->name,
                'nip' => $p->nip ?? $p->username,
                'total_binaan' => $totalBinaanP,
                'sudah_monev' => $sudahMonevCount,
                'persentase' => $pctP,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
            ];
        }

        $persentasePengawasLapor = $totalPengawas > 0 ? round(($pengawasSudahLaporCount / $totalPengawas) * 100, 1) : 0;

        return [
            'rekapKepatuhanPengawas' => $rekapKepatuhanPengawas,
            'totalPengawas' => $totalPengawas,
            'pengawasSudahLaporCount' => $pengawasSudahLaporCount,
            'totalSekolahBinaanWilayah' => $totalSekolahBinaanWilayah,
            'persentasePengawasLapor' => $persentasePengawasLapor,
        ];
    }

    public function index(Request $request)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $user = Auth::user();
        $query = $this->getFilteredMonevQuery($request);
        $kabupatenOptions = $this->getKabupatenOptions($user);

        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        // Aggregated metrics
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

        // --- PROGRESS & PERCENTAGE ANALYTICS ---
        $isPengawas = $user && strtolower($user->role) == 'pengawas';

        $totalSekolahBinaan = 0;
        $sekolahSudahMonevCount = 0;
        $persentaseMonev = 0;
        $sekolahBinaanList = collect();

        if ($isPengawas) {
            $sekolahBinaanList = \App\Models\SekolahbinaanT::where('id_pengawas', $user->id)->with('sekolah')->get();
            $totalSekolahBinaan = $sekolahBinaanList->count();
            $sekolahDimonevIds = $monevList->pluck('sekolah_id')->unique()->toArray();

            $sekolahSudahMonevCount = count(array_intersect($sekolahBinaanList->pluck('id_sekolah')->toArray(), $sekolahDimonevIds));
            $persentaseMonev = $totalSekolahBinaan > 0 ? round(($sekolahSudahMonevCount / $totalSekolahBinaan) * 100, 1) : 0;
        }

        $rekapData = $this->getRekapKepatuhanData($request, $user);
        $rekapKepatuhanPengawas = $rekapData['rekapKepatuhanPengawas'];
        $totalPengawas = $rekapData['totalPengawas'];
        $pengawasSudahLaporCount = $rekapData['pengawasSudahLaporCount'];
        $totalSekolahBinaanWilayah = $rekapData['totalSekolahBinaanWilayah'];
        $persentasePengawasLapor = $rekapData['persentasePengawasLapor'];

        $persentaseSekolahWilayah = $totalSekolahBinaanWilayah > 0 ? round(($totalSekolahDimonev / $totalSekolahBinaanWilayah) * 100, 1) : 0;

        return view('adminNew.dashboard_monev_bosp', compact(
            'monevList', 'year', 'month', 'bulanOptions', 'selectedKabupaten', 'kabupatenOptions',
            'totalSekolahDimonev', 'totalSiswaRiil', 'sekolahSelisihLebih', 'sekolahSelisihKurang',
            'totalRealisasiBosp', 'statusIjopData',
            'isPengawas', 'totalSekolahBinaan', 'sekolahSudahMonevCount', 'persentaseMonev', 'sekolahBinaanList',
            'totalPengawas', 'pengawasSudahLaporCount', 'persentasePengawasLapor',
            'totalSekolahBinaanWilayah', 'persentaseSekolahWilayah', 'rekapKepatuhanPengawas'
        ));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredMonevQuery($request);
        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        $filename = 'Laporan_Monev_BOSP_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonevBospDashboardExport($monevList), $filename);
    }

    public function exportPdf(Request $request)
    {
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $user = Auth::user();
        $query = $this->getFilteredMonevQuery($request);
        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        $totalSekolahDimonev = $monevList->unique('sekolah_id')->count();
        $totalSiswaRiil = $monevList->sum('total_siswa_riil');
        $totalRealisasiBosp = $monevList->sum('realisasi_bosp');

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

        $countSekolahLebih = $monevList->filter(function($item) { return $item->total_siswa_riil > $item->siswa_dinas_bos; })->count();
        $countSekolahKurang = $monevList->filter(function($item) { return $item->total_siswa_riil < $item->siswa_dinas_bos; })->count();
        $countSekolahSesuai = $monevList->filter(function($item) { return $item->total_siswa_riil == $item->siswa_dinas_bos; })->count();

        $rekapData = $this->getRekapKepatuhanData($request, $user);
        $rekapKepatuhanPengawas = $rekapData['rekapKepatuhanPengawas'];
        $totalPengawas = $rekapData['totalPengawas'];
        $pengawasSudahLaporCount = $rekapData['pengawasSudahLaporCount'];
        $totalSekolahBinaanWilayah = $rekapData['totalSekolahBinaanWilayah'];
        $persentasePengawasLapor = $rekapData['persentasePengawasLapor'];

        $persentaseSekolahWilayah = $totalSekolahBinaanWilayah > 0 ? round(($totalSekolahDimonev / $totalSekolahBinaanWilayah) * 100, 1) : 0;

        $selectedKabupatenName = 'Semua Kabupaten / Kota';
        if ($selectedKabupaten !== 'all') {
            $kab = \App\Kabupaten::find($selectedKabupaten);
            if ($kab) {
                $selectedKabupatenName = $kab->nama_kabupaten;
            }
        }

        $targetJenjang = 'ALL';
        if ($user && $user->akses_jenjang) {
            $jArr = json_decode($user->akses_jenjang, true) ?? [];
            if (!empty($jArr)) {
                $targetJenjang = implode(', ', $jArr);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade::loadView('adminNew.export_dashboard_monev_bosp_pdf', compact(
            'monevList', 'year', 'month', 'selectedKabupaten', 'selectedKabupatenName', 'targetJenjang',
            'totalSekolahDimonev', 'totalSiswaRiil', 'totalRealisasiBosp',
            'sekolahSelisihLebih', 'sekolahSelisihKurang', 'countSekolahLebih', 'countSekolahKurang', 'countSekolahSesuai',
            'totalPengawas', 'pengawasSudahLaporCount', 'persentasePengawasLapor',
            'totalSekolahBinaanWilayah', 'persentaseSekolahWilayah', 'rekapKepatuhanPengawas'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Monev_BOSP_' . date('Ymd_His') . '.pdf');
    }
}
