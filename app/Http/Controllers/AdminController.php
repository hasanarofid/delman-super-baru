<?php

namespace App\Http\Controllers;
use App\User;
use App\Profile;
use App\GuruM;
use App\SekolahM;
use App\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\MasterTupoksi;
use App\TanggapanUmpanbalikT;
use App\Models\RencanaKerjaT;
use App\Models\UmpanbalikT;
use App\Traits\StakeholderAccess;

class AdminController extends Controller
{
    use StakeholderAccess;
    public function index()
    {
        $user = Auth::user();
        if ($user) {
            // Periksa apakah pengguna adalah pengawas
            if ($user->role == "Pengawas") {
                // Pengguna sudah login dan adalah pengawas, lanjutkan ke halaman pengawas
                return redirect()->route('pengawas.index');
            } else {
                $user_kabupaten_id = null;
                $akses_kabupaten = [];
                $akses_jenjang = [];

                $total_guru_q = GuruM::where('is_aktif', true);
                $total_sekolah_q = SekolahM::where('is_aktif', true);
                $total_pengawas_q = User::where('role', 'Pengawas');
                $total_stakeholder_q = User::where('role', 'Stakeholder');
                $total_rencankerja_q = RencanaKerjaT::query();
                $total_umpanbalik_q = UmpanbalikT::query();

                $total_guru_q = $this->applyStakeholderFilter($total_guru_q, 'kabupaten_id', 'nama_sekolah', null, 'sekolah');
                $total_sekolah_q = $this->applyStakeholderFilter($total_sekolah_q, 'kabupaten_id', 'nama_sekolah');
                $total_pengawas_q = $this->applyStakeholderFilter($total_pengawas_q, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah');
                $total_stakeholder_q = $this->applyStakeholderFilter($total_stakeholder_q, 'kabupaten_id', null);
                $total_rencankerja_q = $this->applyStakeholderFilter($total_rencankerja_q, 'pengawasnama.kabupaten_id', null, 'pengawasnama');
                $total_umpanbalik_q = $this->applyStakeholderFilter($total_umpanbalik_q, 'pengawasnama.kabupaten_id', null, 'pengawasnama');

                $total_guru = $total_guru_q->count();
                $total_sekolah = $total_sekolah_q->count();
                $total_pengawas = $total_pengawas_q->count();
                $total_stakeholder = $total_stakeholder_q->count();
                $total_rencankerja = $total_rencankerja_q->count();
                $total_umpanbalik = $total_umpanbalik_q->count();

            $master = MasterTupoksi::orderBy('urutan')->get();
            $currentMonth = date('n'); // Numeric representation of the current month (1-12)
        $currentYear = date('Y');  // Current year
        $years = range($currentYear - 5, $currentYear + 5);
         $months = [];

        // Array of month names in Indonesian
        $monthNamesIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Generate the current and next 11 months in Indonesian
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }

        $listPengawas_q = User::where('role', 'pengawas');
        $listPengawas_q = $this->applyStakeholderFilter($listPengawas_q, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah');
        $listPengawas = $listPengawas_q->get();

        // Get available Kabupaten for filters
        $kabupaten_list_q = Kabupaten::query();
        if ($user->role == 'Admin') {
            if ($user->kabupaten_id) {
                $kab = Kabupaten::find($user->kabupaten_id);
                if ($kab) {
                    $kabupaten_list_q->where('kelompok_kabupaten', $kab->kelompok_kabupaten);
                }
            }
        } elseif ($user->role == 'Stakeholder') {
            $akses_kabupaten = json_decode($user->akses_kabupaten, true) ?? [];
            if (!in_array('All', $akses_kabupaten) && !empty($akses_kabupaten)) {
                $kabupaten_list_q->whereIn('id', $akses_kabupaten);
            }
        }
        $listKabupaten = $kabupaten_list_q->orderBy('nama_kabupaten')->get();

                return view('adminNew.index',
                compact(
                    'total_guru',
                    'total_sekolah',
                    'total_pengawas',
                    'total_stakeholder',
                    'total_rencankerja',
                    'total_umpanbalik',
                    'months',
                    'currentYear',
                    'years',
                    'listPengawas',
                    'listKabupaten'
                    ) );
            }
        }

    }

    public function chartData(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        $query = RencanaKerjaT::with('pengawasnama')
        ->selectRaw('id_pengawas, COUNT(*) as total')
        ->groupBy('id_pengawas');

        $query = $this->applyStakeholderFilter($query, 'pengawasnama.kabupaten_id', 'pengawasnama.nama_sekolah', 'pengawasnama', 'pengawasnama.sekolah');

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = rencakakerja_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        if ($kabupaten_filter !== 'all') {
            $query->whereHas('pengawasnama', function($q) use ($kabupaten_filter) {
                $q->where('kabupaten_id', $kabupaten_filter);
            });
        }

        // Apply the month filter
        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        // Apply the year filter (default ke tahun sekarang jika 'all')
        if ($year !== 'all') {
            $query->where('tahun_ajaran', $year);
        } else {
            $query->where('tahun_ajaran', date('Y'));
        }

        // Get the results
        $data = $query->get()
            ->map(function ($item) {
                return [
                    'pengawas' => $item->pengawasnama ? $item->pengawasnama->name : 'Unknown',
                    'total' => $item->total
                ];
            });

        // Return the data as JSON
        return response()->json($data); // Return JSON data for use in the view
    }
    public function chartData2(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $pengawas = $request->input('pengawas', 'all');
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        $query = UmpanbalikT::with('pengawasnama', 'rencanakerja')
            ->selectRaw('id_pelaporan, 
                        COUNT(DISTINCT CONCAT(umpanbalik_t.id_user, "-", umpanbalik_t.id_pelaporan)) as total_umpan_balik,
                        COUNT(DISTINCT CONCAT(tanggapan_umpanbalik_t.id)) as total_respon

                ')
            ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id')
            ->leftJoin('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id')
            // ->whereNotNull('tanggapan_umpanbalik_t.id')  // Abaikan nilai NULL
               ->groupBy('id_pelaporan');

        $query = $this->applyStakeholderFilter($query, 'pengawasnama.kabupaten_id', 'pengawasnama.nama_sekolah', 'pengawasnama', 'pengawasnama.sekolah');

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        if ($kabupaten_filter !== 'all') {
            $query->whereHas('pengawasnama', function($q) use ($kabupaten_filter) {
                $q->where('kabupaten_id', $kabupaten_filter);
            });
        }

        // Apply the filters    COUNT(DISTINCT CONCAT(tanggapan_umpanbalik_t.id_user, "-", tanggapan_umpanbalik_t.id)) as total_respon
        if ($pengawas !== 'all') {
            $query->where('umpanbalik_t.id_pengawas', $pengawas);
        }
        $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
            if ($month !== 'all') $q->where('bulan', $month);
            // Default ke tahun sekarang jika 'all'
            $filterYear = ($year !== 'all') ? $year : date('Y');
            $q->where('tahun_ajaran', $filterYear);
        });
        // dd($query->get());
        // $result = $query->get();
        // dd($result);
        $data = $query->get()->map(function ($item) {
            return [
                'rencana_kerja' => $item->rencanakerja ? $item->rencanakerja->nama_program_kerja : 'Unknown',
                'total_respon' => $item->total_respon,
                'total_umpan_balik' => $item->total_umpan_balik,
            ];
        });

        return response()->json($data);
    }


    public function chartData2lama(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $pengawas = $request->input('pengawas', 'all');

        $query = UmpanbalikT::with('pengawasnama','rencanakerja')
        ->selectRaw('id_pelaporan, COUNT(*) as total')
        ->whereHas('tanggapanUmpanBalik')
        ->groupBy('id_pelaporan');

         // Apply the year filter
         if ($pengawas !== 'all') {
            $query->where('id_pengawas', $pengawas);
        }


         // Apply the month and year filters on the related rencanakerja table
        $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
            if ($month !== 'all') {
                $q->where('bulan', $month);
            }
            // Default ke tahun sekarang jika 'all'
            $filterYear = ($year !== 'all') ? $year : date('Y');
            $q->where('tahun_ajaran', $filterYear);
        });

         // Get the results
         $data = $query->get()
         ->map(function ($item) {
             return [
                'pengawas' => $item->rencanakerja ? $item->rencanakerja->nama_program_kerja : 'Unknown',
                'total' => $item->total
             ];
         });
         return response()->json($data);
    }

    // chartDataRaportPendidikan
    public function chartDataRaportPendidikan(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        $query = RencanaKerjaT::with('aspekprogram')
        ->selectRaw('aspekprogram_id, COUNT(*) as total')
        ->groupBy('aspekprogram_id');

        $query = $this->applyStakeholderFilter($query, 'pengawasnama.kabupaten_id', 'pengawasnama.nama_sekolah', 'pengawasnama', 'pengawasnama.sekolah');

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = rencakakerja_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        if ($kabupaten_filter !== 'all') {
            $query->whereHas('pengawasnama', function($q) use ($kabupaten_filter) {
                $q->where('kabupaten_id', $kabupaten_filter);
            });
        }

        // Apply the month filter
        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        // Apply the year filter (default ke tahun sekarang jika 'all')
        if ($year !== 'all') {
            $query->where('tahun_ajaran', $year);
        } else {
            $query->where('tahun_ajaran', date('Y'));
        }

        // Get the results
        $data = $query->get()
            ->map(function ($item) {
                return [
                    'aspekprogram' => $item->aspekprogram ? $item->aspekprogram->nama : 'Unknown',
                    'total' => $item->total
                ];
            });

        // Return the data as JSON
        return response()->json($data); // Return JSON data for use in the view
    }

    // spider web
    public function getSpiderWebData(Request $request)
    {
        $pengawasId = $request->input('pengawas', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        // Define the query to calculate averages
        $query = TanggapanUmpanbalikT::selectRaw(
            'AVG(
                CASE jawaban_5
                    WHEN "Sangat Baik" THEN 4
                    WHEN "Baik" THEN 3
                    WHEN "Cukup" THEN 2
                    WHEN "Kurang" THEN 1
                    WHEN "Sangat Kurang" THEN 0
                END
            ) as kemampuan_berinteraksi,
            AVG(
                CASE jawaban_6
                    WHEN "Sangat Baik" THEN 4
                    WHEN "Baik" THEN 3
                    WHEN "Cukup" THEN 2
                    WHEN "Kurang" THEN 1
                    WHEN "Sangat Kurang" THEN 0
                END
            ) as menciptakan_suasana,
            AVG(
                CASE jawaban_7
                    WHEN "Sangat Baik" THEN 4
                    WHEN "Baik" THEN 3
                    WHEN "Cukup" THEN 2
                    WHEN "Kurang" THEN 1
                    WHEN "Sangat Kurang" THEN 0
                END
            ) as penguasaan_materi,
            AVG(
                CASE jawaban_8
                    WHEN "Sangat Baik" THEN 4
                    WHEN "Baik" THEN 3
                    WHEN "Cukup" THEN 2
                    WHEN "Kurang" THEN 1
                    WHEN "Sangat Kurang" THEN 0
                END
            ) as kemampuan_komunikasi,
            AVG(
                CASE jawaban_9
                    WHEN "Sangat Baik" THEN 4
                    WHEN "Baik" THEN 3
                    WHEN "Cukup" THEN 2
                    WHEN "Kurang" THEN 1
                    WHEN "Sangat Kurang" THEN 0
                END
            ) as ketepatan_waktu'
        )
        ->join('umpanbalik_t as ut', 'ut.id', '=', 'tanggapan_umpanbalik_t.id_umpanbalik')
        ->join('rencakakerja_t as rt', 'rt.id', '=', 'ut.id_pelaporan');

        // Apply custom stakeholder filters
        $user = Auth::user();
        if ($user && $user->role == 'Admin') {
            if ($user->kabupaten_id) {
                $query->join('users as u', 'u.id', '=', 'rt.id_pengawas')
                      ->where('u.kabupaten_id', $user->kabupaten_id);
            }
        } elseif ($user && $user->role == 'Stakeholder') {
            $akses_kabupaten = json_decode($user->akses_kabupaten, true) ?? [];
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];
            if (!in_array('All', $akses_kabupaten) && !empty($akses_kabupaten)) {
                $query->join('users as u', 'u.id', '=', 'rt.id_pengawas')
                      ->whereIn('u.kabupaten_id', $akses_kabupaten);
            }
            if (!in_array('All', $akses_jenjang) && !empty($akses_jenjang)) {
                $query->whereExists(function($q) use ($akses_jenjang) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('sekolahbinaan_t')
                      ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                      ->whereRaw('sekolahbinaan_t.id_pengawas = rt.id_pengawas')
                      ->where(function($q2) use ($akses_jenjang) {
                          foreach ($akses_jenjang as $jenjang) {
                              $q2->orWhere('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang . '%');
                          }
                      });
                });
            }
        }

        if ($kabupaten_filter !== 'all') {
            // Check if already joined users
            if (!str_contains($query->toSql(), 'join `users` as `u`')) {
                $query->join('users as u', 'u.id', '=', 'rt.id_pengawas');
            }
            $query->where('u.kabupaten_id', $kabupaten_filter);
        }

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = rt.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        // Apply filter based on pengawasId, if specified
        if ($pengawasId !== 'all') {
            $query->where('rt.id_pengawas', $pengawasId);
        }

        // Apply the year filter
        if ($year !== 'all') {
            $query->where('rt.tahun_ajaran', $year);
        } else {
            $query->where('rt.tahun_ajaran', date('Y'));
        }

        // Execute the query to retrieve the averages
        $data = $query->first();

        return response()->json($data);
    }





    public function data()
    {
        return view('adminNew.data');
    }

    // chart terkonfirmasi
    public function chartTerkonfirmasi(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        $query = UmpanbalikT::with('pengawasnama','tanggapanUmpanBalik','rencanakerja')
        ->whereHas('tanggapanUmpanBalik') // hanya ambil yang sudah ada tanggapan
        ->selectRaw('id_pengawas, COUNT(*) as total')
        ->groupBy('id_pengawas');

        $query = $this->applyStakeholderFilter($query, 'pengawasnama.kabupaten_id', null, 'pengawasnama');

        if ($kabupaten_filter !== 'all') {
            $query->whereHas('pengawasnama', function($q) use ($kabupaten_filter) {
                $q->where('kabupaten_id', $kabupaten_filter);
            });
        }

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        // Apply the month and year filters on the related rencanakerja table
        $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
            if ($month !== 'all') {
                $q->where('bulan', $month);
            }
            // Default ke tahun sekarang jika 'all'
            $filterYear = ($year !== 'all') ? $year : date('Y');
            $q->where('tahun_ajaran', $filterYear);
        });

        // Get the results
        $data = $query->get()
            ->map(function ($item) {
                return [
                    'pengawas' => $item->pengawasnama ? $item->pengawasnama->name : 'Unknown',
                    'total' => $item->total
                ];
            });

        // Return the data as JSON
        return response()->json($data); // Return JSON data for use in the view
    }

    // chart pie
    public function chartpie(Request $request)
    {
        $pengawas_filter = $request->input('pengawas', 'all');
        $tahun_filter = $request->input('tahun', date('Y'));
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $query = \App\Models\UmpanbalikT::selectRaw("
                COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, melakukan pengawasan di Sekolah' THEN 1 END) as sekolah,
                COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, melakukan pengawasan secara virtual' THEN 1 END) as by_virtual,
                COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, pengawasan digabungkan dengan sekolah lain' THEN 1 END) as gabungan,
                COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Tidak melakukan pengawasan' THEN 1 END) as tidak
            ")
            ->join('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id')
            ->join('rencakakerja_t as rt', 'rt.id', '=', 'umpanbalik_t.id_pelaporan');

        // Gunakan applyStakeholderFilter untuk memastikan konsistensi dengan grafik lainnya
        $query = $this->applyStakeholderFilter($query, 'pengawasnama.kabupaten_id', 'pengawasnama.nama_sekolah', 'pengawasnama', 'pengawasnama.sekolah');

        if ($kabupaten_filter !== 'all') {
            $query->whereHas('pengawasnama', function($q) use ($kabupaten_filter) {
                $q->where('kabupaten_id', $kabupaten_filter);
            });
        }
        
        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        if ($pengawas_filter !== 'all') {
            $query->where('umpanbalik_t.id_pengawas', $pengawas_filter);
        }

        if ($tahun_filter !== 'all') {
            $query->where('rt.tahun_ajaran', $tahun_filter);
        } else {
            $query->where('rt.tahun_ajaran', date('Y'));
        }

        // Ambil hasil dan bentuk ulang data untuk output JSON
        $data = $query->first(); // Mengambil hasil sebagai satu baris karena kita hanya menghitung jumlah

        $result = [
            [
                'jawaban' => 'Hadir',
                'total' => $data->sekolah,
            ],
            [
                'jawaban' => 'Hadir Virtual',
                'total' => $data->by_virtual,
            ],
            [
                'jawaban' => 'Hadir Dikumpulkan',
                'total' => $data->gabungan,
            ],
            [
                'jawaban' => 'Tidak Hadir',
                'total' => $data->tidak,
            ],
        ];

        return response()->json($result);
    }

    /** get data */
    public function getdata(Request $request){
        if ($request->ajax()) {
            $post = User::with('kabupaten')->where('role','Admin')->latest()->get();
            // dd($post);
            return Datatables::of($post)
                    ->addIndexColumn()
                     ->addColumn('foto', function($row){
                        if($row->foto_profile == 'userdefault.jpg'){
                            $foto = asset('userdefault.jpg');
                        }else{
                            $foto =  route('admin',$row->foto_profile );
                        }

                     return  '<div class="card card-profile"><img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top"></div>';
                    })->addColumn('no_telp', function($row){
                        return !empty($row->no_telp) ? $row->no_telp: '-';
             })
                      ->addColumn('alamat', function($row){
                               return !empty($row->alamat_lengkap) ? $row->alamat_lengkap: '-';
                    })
                      ->addColumn('kabupaten', function($row){
                        return !empty($row->kabupaten->kelompok_kabupaten) ? $row->kabupaten->kelompok_kabupaten : '-';
                    })
                    ->addColumn('action', function($row){
                        if(Auth::user()->role == 'Stakeholder'){
                            return '-';
                        }
                        $btn = '<a href="'.route('admin.edit', $row->id).'" data-toggle="tooltip" class="edit btn btn-primary btn-sm waves-effect waves-light editPost" style="margin-right: 5px;">
                           Edit
                        </a>';
                        $btn .= '<br/><br/>';
                        $btn .= ' <a href="'.route('admin.hapus', $row->id).'" data-toggle="tooltip" data-target="#confirmDeleteModal" data-original-title="Delete" class="btn btn-danger btn-sm waves-effect waves-light deletePost">
                            Delete
                        </a>';
                        return $btn;

                    })
                    ->rawColumns(['no_telp','alamat','action','foto','kabupaten'])
                    ->make(true);
        }
        return view('admin.data');
    }


    public function add(){
        $wilayah = Kabupaten::select('kelompok_kabupaten', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as total'))
             ->groupBy('kelompok_kabupaten')
             ->get();

        // dd($wilayah);
         return view('adminNew.add',compact('wilayah'));
    }

    public function edit($id){
        $model = User::find($id);
        $wilayah = Kabupaten::select('kelompok_kabupaten', DB::raw('MAX(id) as id'), DB::raw('COUNT(*) as total'))
             ->groupBy('kelompok_kabupaten')
             ->get();

        // dd($wilayah);
         return view('adminNew.edit',compact('model','wilayah'));
    }

     /** save data admin */
    public function store(Request $request){
        // dd($request->post());die;
             $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->nip = $request->nip;
            $user->jenjang_jabatan = $request->jenjang_jabatan;
            $user->pangkat = $request->pangkat;
            $user->gol_ruang = $request->gol_ruang;
            $user->foto_profile = 'userdefault.jpg';
            $user->role = 'Admin';
            $user->kabupaten_id =  $request->kabupaten_id;
            $user->password = Hash::make($request->password);
            $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
            $user->save();

            return redirect()->route('admin.data')->with('success', 'admin created successfully');
    }

    /** save data admin */
    public function update($id,Request $request){
        // dd($request->post());die;
            //  $request->validate([
            //     'name' => 'required|string|max:255',
            //     'email' => 'required|email|unique:users',
            //     'password' => 'required|string|min:6',
            // ]);
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->kabupaten_id =  $request->kabupaten_id;

            $user->no_telp = $request->no_telp;
            $user->kota = $request->kota;
            $user->alamat_lengkap = $request->alamat_lengkap;
            $user->kode_area = $request->kode_area;
            $user->save();

             if(isset($request->password)){
            $user->password = Hash::make($request->password);
            $user->update();
        }

            return redirect()->route('admin.data')->with('success', 'admin updated successfully');
    }

    public function hapus($id){
         $user = User::where('id',$id)->delete();
        return redirect()->back()->with('success', 'admin Delete successfully');
    }


    public function getDynamicChartData(Request $request)
    {
        $questionId = $request->input('question_id');
        $pengawasId = $request->input('pengawas', 'all');
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y'));
        $kabupaten_filter = $request->input('kabupaten', 'all');
        $jenjang_filter = $request->input('jenjang', 'all');

        $user = Auth::user();
        $kabupaten_id = ($user->role == 'Stakeholder' || $user->role == 'Admin') ? $user->kabupaten_id : null;

        $query = \App\Models\UmpanbalikAnswer::select('umpanbalik_answers.answer', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->join('umpanbalik_t', 'umpanbalik_answers.id_umpanbalik_t', '=', 'umpanbalik_t.id')
            ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id');

        // Apply custom stakeholder filters
        $user = Auth::user();
        if ($user && $user->role == 'Admin') {
            if ($user->kabupaten_id) {
                $query->join('users', 'umpanbalik_t.id_pengawas', '=', 'users.id')
                      ->where('users.kabupaten_id', $user->kabupaten_id);
            }
        } elseif ($user && $user->role == 'Stakeholder') {
            $akses_kabupaten = json_decode($user->akses_kabupaten, true) ?? [];
            $akses_jenjang = json_decode($user->akses_jenjang, true) ?? [];
            if (!in_array('All', $akses_kabupaten) && !empty($akses_kabupaten)) {
                $query->join('users', 'umpanbalik_t.id_pengawas', '=', 'users.id')
                      ->whereIn('users.kabupaten_id', $akses_kabupaten);
            }
            if (!in_array('All', $akses_jenjang) && !empty($akses_jenjang)) {
                $query->whereExists(function($q) use ($akses_jenjang) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('sekolahbinaan_t')
                      ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                      ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
                      ->where(function($q2) use ($akses_jenjang) {
                          foreach ($akses_jenjang as $jenjang) {
                              $q2->orWhere('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang . '%');
                          }
                      });
                });
            }
        }

        if ($kabupaten_filter !== 'all') {
            if (!str_contains($query->toSql(), 'join `users`')) {
                $query->join('users', 'umpanbalik_t.id_pengawas', '=', 'users.id');
            }
            $query->where('users.kabupaten_id', $kabupaten_filter);
        }

        if ($jenjang_filter !== 'all') {
            $query->whereExists(function($q) use ($jenjang_filter) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('sekolahbinaan_t')
                  ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
                  ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
                  ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
            });
        }

        if ($pengawasId !== 'all') {
            $query->where('umpanbalik_t.id_pengawas', $pengawasId);
        }

        if ($questionId) {
            $query->where('umpanbalik_answers.id_question', $questionId);
        }

        // Apply the month filter
        if ($month !== 'all') {
            $query->where('rencakakerja_t.bulan', $month);
        }

        // Apply the year filter
        if ($year !== 'all') {
            $query->where('rencakakerja_t.tahun_ajaran', $year);
        } else {
            $query->where('rencakakerja_t.tahun_ajaran', date('Y'));
        }

        $data = $query->groupBy('umpanbalik_answers.answer')->get();

        return response()->json($data);
    }
}

