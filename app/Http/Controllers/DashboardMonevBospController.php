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
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $user = Auth::user();
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten']);

        $targetJenjang = 'ALL';
        if ($user) {
            $identifier = strtolower(($user->name ?? '') . ' ' . ($user->username ?? '') . ' ' . ($user->email ?? ''));
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];

            if (in_array('SMA', $akses_jenjang) || strpos($identifier, 'sma') !== false) {
                $targetJenjang = 'SMA';
            } elseif (in_array('SMK', $akses_jenjang) || strpos($identifier, 'smk') !== false) {
                $targetJenjang = 'SMK';
            } else {
                if (strtolower($user->role) != 'pengawas') {
                    $targetJenjang = 'SMK';
                }
            }

            if ($targetJenjang !== 'ALL') {
                $query->whereHas('sekolah', function($q) use ($targetJenjang) {
                    $q->where('nama_sekolah', 'like', '%' . $targetJenjang . '%');
                });
            }
        }

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        // Get Kabupaten Options for Filter Dropdown
        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
            if ($kelompok_kabupaten) {
                $kabupatenOptions = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->get();
                $kabupaten_ids = $kabupatenOptions->pluck('id');
                $query->whereHas('sekolah', function($q) use ($kabupaten_ids) {
                    $q->whereIn('kabupaten_id', $kabupaten_ids);
                });
            } else {
                $kabupatenOptions = \App\Kabupaten::where('id', $user->kabupaten_id)->get();
                $query->whereHas('sekolah', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }
        } else {
            $kabupatenOptions = \App\Kabupaten::orderBy('nama_kabupaten', 'asc')->get();
        }

        // Explicit Filter by Selected Kabupaten
        if ($selectedKabupaten !== 'all') {
            $query->whereHas('sekolah', function($q) use ($selectedKabupaten) {
                $q->where('kabupaten_id', $selectedKabupaten);
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

        // --- PROGRESS & PERCENTAGE ANALYTICS ---
        $isPengawas = $user && strtolower($user->role) == 'pengawas';

        // 1. Analytics for Role Pengawas
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

        // 2. Analytics for Role Admin & Stakeholder (Kabid / Dinas)
        $pengawasQuery = \App\User::where('role', 'Pengawas');

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
            if ($kelompok_kabupaten) {
                $kabupaten_ids = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
                $pengawasQuery->whereIn('kabupaten_id', $kabupaten_ids);
            } else {
                $pengawasQuery->where('kabupaten_id', $user->kabupaten_id);
            }
        }

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

        if ($targetJenjang !== 'ALL') {
            $pengawasQuery->whereExists(function($q) use ($targetJenjang) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = users.id')
                  ->where('sekolah_m.nama_sekolah', 'like', '%' . $targetJenjang . '%');
            });
        }

        $pengawasList = $pengawasQuery->get();

        $rekapKepatuhanPengawas = [];
        $totalPengawas = $pengawasList->count();
        $pengawasSudahLaporCount = 0;
        $totalSekolahBinaanWilayah = 0;

        foreach ($pengawasList as $p) {
            $binaanQueryP = \App\Models\SekolahbinaanT::where('id_pengawas', $p->id);
            if ($targetJenjang !== 'ALL') {
                $binaanQueryP->whereHas('sekolah', function($q) use ($targetJenjang) {
                    $q->where('nama_sekolah', 'like', '%' . $targetJenjang . '%');
                });
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
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');
        $selectedKabupaten = $request->input('kabupaten_id', 'all');

        $user = Auth::user();
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten']);

        $targetJenjang = 'ALL';
        if ($user) {
            $identifier = strtolower(($user->name ?? '') . ' ' . ($user->username ?? '') . ' ' . ($user->email ?? ''));
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];

            if (in_array('SMA', $akses_jenjang) || strpos($identifier, 'sma') !== false) {
                $targetJenjang = 'SMA';
            } elseif (in_array('SMK', $akses_jenjang) || strpos($identifier, 'smk') !== false) {
                $targetJenjang = 'SMK';
            } else {
                if (strtolower($user->role) != 'pengawas') {
                    $targetJenjang = 'SMK';
                }
            }

            if ($targetJenjang !== 'ALL') {
                $query->whereHas('sekolah', function($q) use ($targetJenjang) {
                    $q->where('nama_sekolah', 'like', '%' . $targetJenjang . '%');
                });
            }
        }

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
            if ($kelompok_kabupaten) {
                $kabupaten_ids = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
                $query->whereHas('sekolah', function($q) use ($kabupaten_ids) {
                    $q->whereIn('kabupaten_id', $kabupaten_ids);
                });
            } else {
                $query->whereHas('sekolah', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }
        }

        if ($selectedKabupaten !== 'all') {
            $query->whereHas('sekolah', function($q) use ($selectedKabupaten) {
                $q->where('kabupaten_id', $selectedKabupaten);
            });
        }

        if ($user && strtolower($user->role) == 'pengawas') {
            $query->where('pengawas_id', $user->id);
        }

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
        $query = MonevBosp::with(['pengawas', 'sekolah.kabupaten']);

        $targetJenjang = 'ALL';
        if ($user) {
            $identifier = strtolower(($user->name ?? '') . ' ' . ($user->username ?? '') . ' ' . ($user->email ?? ''));
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];

            if (in_array('SMA', $akses_jenjang) || strpos($identifier, 'sma') !== false) {
                $targetJenjang = 'SMA';
            } elseif (in_array('SMK', $akses_jenjang) || strpos($identifier, 'smk') !== false) {
                $targetJenjang = 'SMK';
            } else {
                if (strtolower($user->role) != 'pengawas') {
                    $targetJenjang = 'SMK';
                }
            }

            if ($targetJenjang !== 'ALL') {
                $query->whereHas('sekolah', function($q) use ($targetJenjang) {
                    $q->where('nama_sekolah', 'like', '%' . $targetJenjang . '%');
                });
            }
        }

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
            if ($kelompok_kabupaten) {
                $kabupaten_ids = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
                $query->whereHas('sekolah', function($q) use ($kabupaten_ids) {
                    $q->whereIn('kabupaten_id', $kabupaten_ids);
                });
            } else {
                $query->whereHas('sekolah', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }
        }

        if ($selectedKabupaten !== 'all') {
            $query->whereHas('sekolah', function($q) use ($selectedKabupaten) {
                $q->where('kabupaten_id', $selectedKabupaten);
            });
        }

        if ($user && strtolower($user->role) == 'pengawas') {
            $query->where('pengawas_id', $user->id);
        }

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

        // Analytics for Admin & Stakeholder
        $pengawasQuery = \App\User::where('role', 'Pengawas');

        if ($user && $user->role == 'Stakeholder' && $user->kabupaten_id) {
            $kelompok_kabupaten = \App\Kabupaten::find($user->kabupaten_id)->kelompok_kabupaten ?? null;
            if ($kelompok_kabupaten) {
                $kabupaten_ids = \App\Kabupaten::where('kelompok_kabupaten', $kelompok_kabupaten)->pluck('id');
                $pengawasQuery->whereIn('kabupaten_id', $kabupaten_ids);
            } else {
                $pengawasQuery->where('kabupaten_id', $user->kabupaten_id);
            }
        }

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

        if ($targetJenjang !== 'ALL') {
            $pengawasQuery->whereExists(function($q) use ($targetJenjang) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = users.id')
                  ->where('sekolah_m.nama_sekolah', 'like', '%' . $targetJenjang . '%');
            });
        }

        $pengawasList = $pengawasQuery->get();

        $rekapKepatuhanPengawas = [];
        $totalPengawas = $pengawasList->count();
        $pengawasSudahLaporCount = 0;
        $totalSekolahBinaanWilayah = 0;

        foreach ($pengawasList as $p) {
            $binaanQueryP = \App\Models\SekolahbinaanT::where('id_pengawas', $p->id);
            if ($targetJenjang !== 'ALL') {
                $binaanQueryP->whereHas('sekolah', function($q) use ($targetJenjang) {
                    $q->where('nama_sekolah', 'like', '%' . $targetJenjang . '%');
                });
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

            $rekapKepatuhanPengawas[] = [
                'nama' => $p->name,
                'nip' => $p->nip ?? $p->username,
                'total_binaan' => $totalBinaanP,
                'sudah_monev' => $sudahMonevCount,
                'persentase' => $pctP,
            ];
        }

        $persentasePengawasLapor = $totalPengawas > 0 ? round(($pengawasSudahLaporCount / $totalPengawas) * 100, 1) : 0;
        $persentaseSekolahWilayah = $totalSekolahBinaanWilayah > 0 ? round(($totalSekolahDimonev / $totalSekolahBinaanWilayah) * 100, 1) : 0;

        $selectedKabupatenName = 'Semua Kabupaten / Kota';
        if ($selectedKabupaten !== 'all') {
            $kab = \App\Kabupaten::find($selectedKabupaten);
            if ($kab) {
                $selectedKabupatenName = $kab->nama_kabupaten;
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
