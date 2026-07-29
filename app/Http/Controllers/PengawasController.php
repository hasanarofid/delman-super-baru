<?php

namespace App\Http\Controllers;

use App\Models\RencanaKerjaT;
use App\Models\UmpanbalikT;
use App\Profile;
use App\TanggapanUmpanbalikT;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\SekolahM;
use Barryvdh\DomPDF\Facade as PDF;
class PengawasController extends Controller
{
    //index
    public function index(){
       // Periksa apakah pengguna sudah login
       if (Auth::check()) {
        // Periksa apakah pengguna adalah pengawas
        if (Auth::user()->role == "Pengawas") {
            // Pengguna sudah login dan adalah pengawas, lanjutkan ke halaman pengawas
            $tahunini = date('Y');  // Current year
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
            for ($i = 0; $i < 12; $i++) {
                $timestamp = strtotime("+$i month");
                $monthNumber = date('n', $timestamp);
                $months[] = [
                    'value' => $monthNumber,                // Month number (1-12)
                    'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
                ];
            }
            $bulanini = $months[0]['name'];
            $totalRencankerja = RencanaKerjaT::where('bulan',$bulanini)
            ->where('tahun_ajaran',$tahunini)
            ->where('id_pengawas',Auth::user()->id)
            ->count();

            $listsekolahdilayani = RencanaKerjaT::with('kategoriprogram')->where('bulan',$bulanini)
            ->where('tahun_ajaran',$tahunini)
            ->where('id_pengawas',Auth::user()->id)
            ->get();

            $schoolKegiatanCount = [];
            foreach ($listsekolahdilayani as $item) {
                $sekolahIds = explode(',', $item->sekolah_id);
                foreach ($sekolahIds as $id) {
                    $id = trim($id);
                    if ($id) {
                        $schoolKegiatanCount[$id] = ($schoolKegiatanCount[$id] ?? 0) + 1;
                    }
                }
            }


            $sekolahdilayani = 0;
            $uniqueSekolahIds = [];
            $listrencanakerja = [];
            
            foreach ($listsekolahdilayani as $value) {
                $sekolahIds = explode(',', $value->sekolah_id);
            
                foreach ($sekolahIds as $id) {
                    // Cek apakah sekolah_id sudah pernah ditambahkan sebelumnya
                    if (!in_array($id, $uniqueSekolahIds)) {
                        $uniqueSekolahIds[] = $id; // Tambahkan sekolah_id ke daftar unik
                        $sekolahdilayani++;        // Tambah hitungan sekolah dilayani
                    }
                }
            }
                // dd($sekolahdilayani);
              

                // dd($sekolahdilayani);

            // dd($totalRencankerja);
            $currentYear2 = date('Y');  // Current year
            $years2 = range($currentYear2 - 5, $currentYear2 + 5);
            $months2 = [];
        
            // Array of month names in Indonesian
            $monthNamesIndo2 = [
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
                $months2[] = [
                    'value' => $monthNumber,                // Month number (1-12)
                    'name' => $monthNamesIndo2[$monthNumber] // Full month name in Indonesian
                ];
            }

            return view('dashboard_pengawas.home',
            compact(
                'months2',
                'currentYear2',    
                'years2',  
                'totalRencankerja',
                'sekolahdilayani',
                'listsekolahdilayani',
                'schoolKegiatanCount'
                )
        );
        } else {
            // Pengguna sudah login, namun bukan pengawas, arahkan ke halaman 403 Forbidden
            abort(403);
        }
        } else {
            // Pengguna belum login, arahkan ke halaman login pengawas
            return redirect('/pengawas/login');
        }
    }

    //edit profile
    public function editprofile(){
        $user = User::with('profile')->find(Auth::user()->id);
        return view('dashboard_pengawas.editprofile',compact('user'));
    }
    //update profile
    public function updateprofile(Request $request){
        $user = User::find(Auth::user()->id);
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');

            // Generate a unique name based on the current date and time.
            $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        
            // Store the image in the "blog" directory within the "shared" disk.
            $request->foto->storeAs('pengawas', $imageName, 'shared');

            $user->foto_profile = $imageName;
        }
        $user->name = $request->post('nama');
        $user->no_telp = $request->post('telp');
        $user->save();
        $profile = Profile::where('user_id',$user->id)->first();
        $profile->alamat_lengkap = $request->post('alamat');
        $profile->no_telp = $request->post('telp');
        $profile->homepage = $request->post('homepage');
        $profile->bio = $request->post('profile');
        $profile->save();
        return redirect()->route('pengawas.editprofile')->with('success', 'Profile berhasil diupdate!');

    }

    //ubahpassword
    public function ubahpassword(Request $request) {
        // Validasi input
        $request->validate([
            'passl' => 'required',
            'passb' => 'required|string|min:8',
            'passu' => 'required|string|min:8',
        ]);
    
        // Ambil pengguna yang sedang login
        $user = User::find(Auth::user()->id);
    
        // Periksa apakah password lama yang dimasukkan benar
        if (!Hash::check($request->passl, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai.');
        }

        // Periksa apakah password baru dan ulangi password sama
        if ($request->passb != $request->passu) {
            return back()->with('error', 'Password baru tidak sesuai dengan ulangi password.');
        }
    
        // Update password baru dengan Hash::make()
        $user->password = Hash::make($request->passb);
        $user->save();
    
        return redirect()->back()->with('success_pass', 'Password berhasil diubah.');
    }



    public function chartData(Request $request)
{
    $month = $request->input('bln', 'all');
    $year = $request->input('tahun', 'all');

    // Daftar nama bulan dalam bahasa Indonesia
    $monthNamesIndo2 = [
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

    // Hitung 6 bulan terakhir
    $dates = [];
    for ($i = 0; $i < 6; $i++) {
        $date = now()->subMonths($i);
        $dates[] = [
            'month' => $date->month,
            'month_name' => $monthNamesIndo2[$date->month],
            'year' => $date->year,
            'total' => 0 // Default value to 0
        ];
    }

    // Query data berdasarkan bulan dan tahun untuk 6 bulan terakhir
    $query = RencanaKerjaT::with('pengawasnama')
        ->selectRaw('id_pengawas, bulan, tahun_ajaran, COUNT(*) as total')
        ->where('id_pengawas', Auth::user()->id)
        ->where(function($q) use ($dates) {
            foreach ($dates as $date) {
                $q->orWhere(function($subQuery) use ($date) {
                    $subQuery->where('bulan', $date['month_name'])
                             ->where('tahun_ajaran', $date['year']);
                });
            }
        })
        ->groupBy('id_pengawas', 'bulan', 'tahun_ajaran')
        ->get();
        // dd($query);

    // Mapkan hasil query ke array $dates
    foreach ($query as $item) {
        foreach ($dates as &$date) {
            if ($date['month_name'] == $item->bulan && $date['year'] == $item->tahun_ajaran) {
                $date['total'] = $item->total;
            }
        }
    }

    // Buat data dalam format yang diperlukan oleh chart.js
    $chartData = [
        'labels' => array_column($dates, 'month_name'), // Nama bulan dalam bahasa Indonesia
        'datasets' => [
            [
                'label' => 'Jumlah Rencana Kerja',
                'data' => array_column($dates, 'total'), // Nilai total dari setiap bulan
                'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                'borderColor' => 'rgba(153, 102, 255, 1)',
                'borderWidth' => 1
            ]
        ]
    ];

    return response()->json($chartData);
}



    public function chartData3(Request $request)
    {
       // Daftar nama bulan dalam bahasa Indonesia
// Daftar nama bulan dalam bahasa Indonesia
$monthNamesIndo2 = [
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

// Hitung 6 bulan terakhir
$dates = [];
for ($i = 0; $i < 6; $i++) {
    $date = now()->subMonths($i);
    $dates[] = [
        'month' => $date->month,
        'month_name' => $monthNamesIndo2[$date->month],
        'year' => $date->year,
        'total' => 0 // Nilai default ke 0
    ];
}
// Ambil 6 bulan terakhir berdasarkan created_at
$startDate = now()->subMonths(5)->startOfMonth();
$endDate = now()->endOfMonth();

// Query utama
$query = UmpanbalikT::with('pengawasnama', 'rencanakerja') // Pastikan relasi rencanakerja sudah ada
    ->selectRaw('rencakakerja_t.bulan, rencakakerja_t.tahun_ajaran, COUNT(DISTINCT tanggapan_umpanbalik_t.id_umpanbalik) as total') // Hitung umpan balik yang sudah ditanggapi saja
    ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id') // Join dengan tabel rencakakerja_t
    ->join('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id') // Join dengan tanggapan_umpanbalik_t
    ->whereBetween('umpanbalik_t.created_at', [$startDate, $endDate])
    ->where('umpanbalik_t.id_pengawas', Auth::user()->id)
    ->groupBy('rencakakerja_t.bulan', 'rencakakerja_t.tahun_ajaran') // Pengelompokan berdasarkan bulan dan tahun_ajaran
    ->get();


// Mapkan hasil query ke array $dates
foreach ($dates as &$date) {
    $date['total'] = 0; // Inisialisasi total agar ada default 0
}

// Mapkan hasil query ke array $dates
foreach ($query as $item) {
    foreach ($dates as &$date) {
        if ($date['month_name'] == $item->bulan && $date['year'] == $item->tahun_ajaran) {
            $date['total'] += $item->total; // Set nilai total untuk bulan dan tahun yang sesuai
        }
    }
}
unset($date); // Bersihkan referensi variabel

// Buat data dalam format yang diperlukan oleh chart.js
$chartData = [
    'labels' => array_column($dates, 'month_name'), // Nama bulan dalam bahasa Indonesia
    'datasets' => [
        [
            'label' => 'Jumlah Umpan Balik',
            'data' => array_column($dates, 'total'), // Nilai total dari setiap bulan
            'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
            'borderColor' => 'rgba(153, 102, 255, 1)',
            'borderWidth' => 1
        ]
    ]
];

// dd($chartData);


return response()->json($chartData);




         // Get the results
        //  $data = $query->get()
        //  ->map(function ($item) {
        //      return [
        //         'pengawas' => $item->rencanakerja ? $item->rencanakerja->nama_program_kerja : 'Unknown',
        //         'total' => $item->total
        //      ];
        //  });
        //  return response()->json($data);
    }

    public function dashboard(){
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

        $user = Auth::user();
        $pesanStakeholder = null;
        if ($user) {
            $pesanStakeholder = \App\Models\PesanStakeholder::with('stakeholder')
                ->where('is_active', 1)
                ->where(function ($q) use ($user) {
                    if ($user->kabupaten_id) {
                        $q->where('kabupaten_id', $user->kabupaten_id)->orWhereNull('kabupaten_id');
                    } else {
                        $q->whereNull('kabupaten_id');
                    }
                })
                ->latest()
                ->first();
        }

        return view('dashboard_pengawas.dashboard',
        compact(
            'months',
            'currentYear',    
            'years',
            'pesanStakeholder'
            )
        );


    }

      // spider web
      public function getSpiderWebDataPengawas(Request $request)
      {

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
          ->join('rencakakerja_t as rt', 'rt.id', '=', 'ut.id_pelaporan')
          ->where('rt.id_pengawas', Auth::user()->id);
      
        
      
          // Execute the query to retrieve the averages
          $data = $query->first();
      
          return response()->json($data);
      }

      public function chartTerkonfirmasiPengawas(Request $request)
    {
        $monthNamesIndo2 = [
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
        
        // Hitung 6 bulan terakhir
        $dates = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $dates[] = [
                'month' => $date->month,
                'month_name' => $monthNamesIndo2[$date->month],
                'year' => $date->year,
                'total' => 0 // Nilai default ke 0
            ];
        }


        $query = UmpanbalikT::with('pengawasnama','tanggapanUmpanBalik','rencanakerja')
         ->selectRaw('umpanbalik_t.id_pengawas, rencakakerja_t.bulan, rencakakerja_t.tahun_ajaran, COUNT(*) as total')
        ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id')
        ->whereHas('tanggapanUmpanBalik') // hanya ambil yang sudah ada tanggapan
        ->where('umpanbalik_t.id_pengawas',Auth::user()->id)
         ->whereHas('rencanakerja', function($q) use ($dates) {
            foreach ($dates as $date) {
                $q->orWhere(function($subQuery) use ($date) {
                    $subQuery->where('rencakakerja_t.bulan', $date['month_name']) // Kolom bulan dari tabel rencakakerja_t
                             ->where('rencakakerja_t.tahun_ajaran', $date['year']); // Kolom tahun_ajaran dari tabel rencakakerja_t
                });
            }
        })
        ->groupBy('umpanbalik_t.id_pengawas', 'rencakakerja_t.bulan', 'rencakakerja_t.tahun_ajaran')
        ->get();
       
        foreach ($query as $item) {
            foreach ($dates as &$date) {
                if ($date['month_name'] == $item->bulan && $date['year'] == $item->tahun_ajaran) {
                    $date['total'] = $item->total;
                }
            }
        }
        
        // Buat data dalam format yang diperlukan oleh chart.js
        $chartData = [
            'labels' => array_column($dates, 'month_name'), // Nama bulan dalam bahasa Indonesia
            'datasets' => [
                [
                    'label' => 'Jumlah Umpan Balik',
                    'data' => array_column($dates, 'total'), // Nilai total dari setiap bulan
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
        
        // Menambahkan data rencanakerja ke chart
        foreach ($query as $item) {
            // Jika ada data untuk program kerja, tambahkan ke data chart
            $chartData['datasets'][0]['pengawas'][] = $item->pengawasnama ? $item->pengawasnama->name : 'Unknown';
        }
        // dd($chartData);
        // ->selectRaw('id_pelaporan, rencakakerja_t.bulan, rencakakerja_t.tahun_ajaran, COUNT(*) as total') // Pastikan kolom bulan dan tahun_ajaran diambil dari tabel rencakakerja_t
        // ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id') // Join dengan tabel rencakakerja_t
        // ->whereHas('tanggapanUmpanBalik')
        // ->where('rencakakerja_t.id_pengawas', Auth::user()->id)
        // ->whereHas('rencanakerja', function($q) use ($dates) {
        //     foreach ($dates as $date) {
        //         $q->orWhere(function($subQuery) use ($date) {
        //             $subQuery->where('rencakakerja_t.bulan', $date['month_name']) // Kolom bulan dari tabel rencakakerja_t
        //                      ->where('rencakakerja_t.tahun_ajaran', $date['year']); // Kolom tahun_ajaran dari tabel rencakakerja_t
        //         });
        //     }
        // })
        // ->groupBy('id_pelaporan', 'rencakakerja_t.bulan', 'rencakakerja_t.tahun_ajaran') // Pengelompokan berdasarkan bulan dan tahun_ajaran dari tabel rencanakerja_t
        // ->get();
                // Apply the month and year filters on the related rencanakerja table
        // $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
        //     if ($month !== 'all') {
        //         $q->where('bulan', $month);
        //     }
        //     if ($year !== 'all') {
        //         $q->where('tahun_ajaran', $year);
        //     }
        // });

        // Get the results
        // $data = $query->get()
        //     ->map(function ($item) {
        //         return [
        //             'pengawas' => $item->pengawasnama ? $item->pengawasnama->name : 'Unknown',
        //             'total' => $item->total
        //         ];
        //     });

        // Return the data as JSON
        return response()->json($chartData); // Return JSON data for use in the view
    }

     // chartDataRaportPendidikan
     public function chartDataRaportPendidikan(Request $request)
     {
         $month = $request->input('bln', 'all');
         $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
 
         $query = RencanaKerjaT::with('aspekprogram')
         ->selectRaw('aspekprogram_id, COUNT(*) as total')
         ->where('id_pengawas',Auth::user()->id)
         ->groupBy('aspekprogram_id');
 
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

      // chart pie
    public function chartpie(Request $request)
    {
        $pengawas = $request->input('pengawas', 'all');
    
        // Buat query untuk menghitung jumlah masing-masing jenis jawaban di jawaban_4
        $query = TanggapanUmpanbalikT::selectRaw("
                COUNT(CASE WHEN jawaban_4 = 'Ya, melakukan pendampingan di Sekolah' THEN 1 END) as sekolah,
                COUNT(CASE WHEN jawaban_4 = 'Ya, melakukan pendampingan secara virtual' THEN 1 END) as by_virtual,
                COUNT(CASE WHEN jawaban_4 = 'Ya, pendampingan digabungkan dengan sekolah lain' THEN 1 END) as gabungan,
                COUNT(CASE WHEN jawaban_4 = 'Tidak melakukan pendampingan' THEN 1 END) as tidak
            ")
            ->join('umpanbalik_t as ut', 'ut.id', '=', 'tanggapan_umpanbalik_t.id_umpanbalik')
            ->join('rencakakerja_t as rt', 'rt.id', '=', 'ut.id_pelaporan')
            ->where('rt.id_pengawas',Auth::user()->id);
    
        // Tambahkan filter untuk pengawas jika ada
        // if ($pengawas !== 'all') {
        //     $query->where('rt.id_pengawas', $pengawas);
        // }
    
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

    public function chartData2Lama(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $pengawas = $request->input('pengawas', 'all');
    
        $query = UmpanbalikT::with('pengawasnama', 'rencanakerja')
            ->selectRaw('id_pelaporan, COUNT(DISTINCT umpanbalik_t.id) as total_respon, 
            COUNT(DISTINCT tanggapan_umpanbalik_t.id_umpanbalik) as total')
            ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id')
            ->leftJoin('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id')
            ->where('umpanbalik_t.id_pengawas',Auth::user()->id)
             ->groupBy('id_pelaporan');
    
        // Apply the filters
        if ($pengawas !== 'all') {
            $query->where('umpanbalik_t.id_pengawas', $pengawas);
        }
        $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
            if ($month !== 'all') $q->where('bulan', $month);
            // Default ke tahun sekarang jika 'all'
            $filterYear = ($year !== 'all') ? $year : date('Y');
            $q->where('tahun_ajaran', $filterYear);
        });
    
        $data = $query->get()->map(function ($item) {
            return [
                'rencana_kerja' => $item->rencanakerja ? $item->rencanakerja->nama_program_kerja : 'Unknown',
                'total_respon' => $item->total_respon,
                'total_sekolah' => $item->total,
            ];
        });
    
        return response()->json($data);
    }

    public function chartData2(Request $request)
    {
        $month = $request->input('bln', 'all');
        $year = $request->input('tahun', date('Y')); // Default ke tahun sekarang
        $pengawas = $request->input('pengawas', 'all');

        $query = UmpanbalikT::with('pengawasnama', 'rencanakerja')
            ->selectRaw('id_pelaporan,
                                    COUNT(DISTINCT CONCAT(umpanbalik_t.id_user, "-", umpanbalik_t.id_pelaporan)) as total_umpan_balik,
            COUNT(DISTINCT tanggapan_umpanbalik_t.id) as total_respon
            ')
            ->join('rencakakerja_t', 'umpanbalik_t.id_pelaporan', '=', 'rencakakerja_t.id')
            ->leftJoin('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id')
            ->where('umpanbalik_t.id_pengawas',Auth::user()->id)
             ->groupBy('id_pelaporan');

        // Apply the filters
        if ($pengawas !== 'all') {
            $query->where('umpanbalik_t.id_pengawas', $pengawas);
        }
        $query->whereHas('rencanakerja', function ($q) use ($month, $year) {
            if ($month !== 'all') $q->where('bulan', $month);
            // Default ke tahun sekarang jika 'all'
            $filterYear = ($year !== 'all') ? $year : date('Y');
            $q->where('tahun_ajaran', $filterYear);
        });

        $data = $query->get()->map(function ($item) {
            return [
                'rencana_kerja' => $item->rencanakerja ? $item->rencanakerja->nama_program_kerja : 'Unknown',
                'total_respon' => $item->total_respon,
                'total_umpan_balik' => $item->total_umpan_balik,
            ];
        });

        return response()->json($data);
    }

    



    public function getDynamicChartData(Request $request)
    {
        $questionId = $request->input('question_id');
        $user = Auth::user();

        if (!$questionId) {
            return response()->json(['error' => 'Question ID is required'], 400);
        }

        $data = \App\Models\UmpanbalikAnswer::select('umpanbalik_answers.answer', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->join('umpanbalik_t', 'umpanbalik_answers.id_umpanbalik_t', '=', 'umpanbalik_t.id')
            ->where('umpanbalik_t.id_pengawas', $user->id)
            ->where('umpanbalik_answers.id_question', $questionId)
            ->groupBy('umpanbalik_answers.answer')
            ->get();

        return response()->json($data);
    }

    public function exportDashboardKinerja(Request $request)
    {
        $user = User::with('profile')->find(Auth::user()->id);
        
        // Data for tables (similar to index method)
        $tahunini = date('Y');
        $monthNamesIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanini = $monthNamesIndo[date('n')];

        $totalRencankerja = RencanaKerjaT::where('bulan', $bulanini)
            ->where('tahun_ajaran', $tahunini)
            ->where('id_pengawas', $user->id)
            ->count();

        $listsekolahdilayani = RencanaKerjaT::with('kategoriprogram')->where('bulan', $bulanini)
            ->where('tahun_ajaran', $tahunini)
            ->where('id_pengawas', $user->id)
            ->get();

        $schoolKegiatanCount = [];
        foreach ($listsekolahdilayani as $item) {
            $sekolahIds = explode(',', $item->sekolah_id);
            foreach ($sekolahIds as $id) {
                $id = trim($id);
                if ($id) {
                    $schoolKegiatanCount[$id] = ($schoolKegiatanCount[$id] ?? 0) + 1;
                }
            }
        }

        $uniqueSekolahIds = [];
        $sekolahdilayani = 0;
        foreach ($listsekolahdilayani as $value) {
            $sekolahIds = explode(',', $value->sekolah_id);
            foreach ($sekolahIds as $id) {
                if (!in_array($id, $uniqueSekolahIds)) {
                    $uniqueSekolahIds[] = $id;
                    $sekolahdilayani++;
                }
            }
        }

        $listSekolahBinaan = [];
        if (!empty($uniqueSekolahIds)) {
            $listSekolahBinaan = SekolahM::whereIn('id', $uniqueSekolahIds)->get();
        }

        $data = [
            'user' => $user,
            'bulan' => $bulanini,
            'tahun' => $tahunini,
            'totalRencankerja' => $totalRencankerja,
            'sekolahdilayani' => $sekolahdilayani,
            'listsekolahdilayani' => $listsekolahdilayani,
            'listSekolahBinaan' => $listSekolahBinaan,
            'schoolKegiatanCount' => $schoolKegiatanCount,
            'generateDate' => now()->format('d F Y'),
            
            // Charts (Base64 from request)
            'chart_rencana' => $request->input('chart_rencana'),
            'chart_umpanbalik' => $request->input('chart_umpanbalik'),
            'chart_kompetensi' => $request->input('chart_kompetensi'),
            'chart_realisasi' => $request->input('chart_realisasi'),
            'chart_terkonfirmasi' => $request->input('chart_terkonfirmasi'),
            'chart_raport' => $request->input('chart_raport'),
            'chart_q1' => $request->input('chart_q1'),
            'chart_q2' => $request->input('chart_q2'),
            'chart_q4' => $request->input('chart_q4'),
        ];

        $pdf = PDF::loadView('dashboard_pengawas.export_dashboard_pdf', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Dashboard_Kinerja_Pengawas.pdf');
    }
}

