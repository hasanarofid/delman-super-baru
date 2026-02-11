<?php

namespace App\Http\Controllers;

use App\GuruM;
use App\Models\RencanaKerjaT;
use App\Models\Kategory;
use App\Models\UmpanbalikT;
use App\SekolahM;
use App\TanggapanUmpanbalikT;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
class DokumentasipendampinganController extends Controller
{
    public function indexpengawas(){

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
        $start = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $monthNumber = $date->month;
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }

        $listPengawas = User::where('role','pengawas')->get();
        $kategori = Kategory::all();

        return view('dashboard_pengawas.umpanbalik.dokumentasi',compact('listPengawas',
        'months',
        'currentYear',
        'years',
        'kategori'
    ));
    }




    public function index(){
        $user = Auth::user();
        $queryPengawas = User::where('role','pengawas');
        
        if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
            $queryPengawas->where('kabupaten_id', $user->kabupaten_id);
        }
        
        $listPengawas = $queryPengawas->get();

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
        $start = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $monthNumber = $date->month;
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }

        $kategori = Kategory::all();

        return view('dokumentasipendampingan.index',compact('listPengawas',
        'months',
        'currentYear',
        'years',
        'kategori'
    ));
    }

    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            $pengawas = $request->input('pengawas', 'all');
            $tahun = $request->input('tahun', 'all');
            $bln = $request->input('bln', 'all');
            $kategori = $request->input('kategori', 'all');
            
            $monthNamesIndo = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
            ];

            // Cek apakah nama bulan sesuai dengan bulan yang diterima dalam bahasa Indonesia
            $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

            // Unified Query using UmpanbalikT
            $post = UmpanbalikT::with('rencanakerja', 'answers.question', 'tanggapanUmpanBalik', 'pengawasnama', 'user.sekolah')
                ->whereNotNull('submitted_at')
                ->latest('submitted_at');

            // Admin/Stakeholder Filter: Only show data from their Kabupaten
            if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
                $post->whereHas('pengawasnama', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }

             // Apply filter for 'bln' (bulan)
             if ($bln !== 'all') {
                $post->whereMonth('submitted_at', $monthNumber);
            }
            if ($tahun !== 'all') {
                $post->whereYear('submitted_at', $tahun);
            }

            if ($pengawas !== 'all') {
                 $post->where('id_pengawas', $pengawas);
            }

            if ($kategori !== 'all') {
                if ($kategori === 'rhk3') {
                     $post->whereColumn('id_user', 'id_pengawas');
                } else {
                    $post->whereHas('rencanakerja', function ($q) use ($kategori) {
                        $q->where('kategoriprogram_id', $kategori);
                    });
                }
            }


            return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('tanggal', function($row){
                    return !empty($row->rencanakerja->created_at) ? $row->rencanakerja->created_at->format('d M Y') : '-';
                })
                ->addColumn('foto', function($row){
                     // Priority 1: Dynamic Image (Q13 or generic file input)
                     $fileAnswer = $row->answers->first(function($a){
                        return $a->id_question == 13 || optional($a->question)->type_input == 'file';
                   });

                   if($fileAnswer && !empty($fileAnswer->answer)){
                        $fotoUrl = route('umpanbalik.dynamic.file', $fileAnswer->answer);
                        return  ' <img src="'.$fotoUrl.'" height="100px" alt="Bukti" class="card-img-top">';
                   }
                   
                   // Priority 2: Legacy Image
                   $legacy = $row->tanggapanUmpanBalik->first();
                   if($legacy && !empty($legacy->foto)){
                       $foto = route('umpanbalikfoto', $legacy->foto);
                       return  ' <img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top">';
                   }

                   return '-';
                })

                ->addColumn('program', function($row){
                    return !empty($row->rencanakerja) ? $row->rencanakerja->nama_program_kerja : '-';
                })
                ->addColumn('pengawas', function($row){
                    return !empty($row->pengawasnama) ? $row->pengawasnama->name : '-';
                })
                ->addColumn('nama_sekolah', function($row) {
                    // Try getting Guru -> Sekolah
                    $cariguru = GuruM::find($row->id_user);
                    if ($cariguru) {
                         $sekolah = SekolahM::find($cariguru->sekolah_id);
                         return $sekolah ? $sekolah->nama_sekolah : '-';
                    }
                    // Fallback: Check if user is the supervisor (Self-Reflection)
                    if ($row->id_user == $row->id_pengawas) {
                         return 'Mandiri (Refleksi Pengawas)';
                    }
                    return '-';
                })
                ->addColumn('rtl_status', function($row) {
                    $rtlStatus = $row->is_rtl == 1 ? 'Sudah dilakukan' : 'Belum dilakukan';
                    $rtlDate = $row->tgl_rtl ? ' (' . $row->tgl_rtl->format('Y-m-d H:i:s') . ')' : '';
                    return $rtlStatus . $rtlDate;
                })
                ->addColumn('catatan_rtl', function($row) {
                    return $row->catatan_rtl ?? '-';
                })
                ->rawColumns(['tanggal', 'foto', 'program', 'pengawas', 'nama_sekolah', 'rtl_status', 'catatan_rtl'])
                ->make(true);
           }
    }

    public function exportPDF(Request $request)
    {
        // Retrieve filter values from the request
        $pengawas = $request->input('pengawas', 'all');
        $tahun = $request->input('tahun', 'all');
        $bln = $request->input('bln', 'all');
        $search = $request->input('search', '');
        $kategori = $request->input('kategori', 'all');

        // Define month names mapping for Indonesian months
        $monthNamesIndo = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        // Convert the month name to the corresponding number, or use 'all'
        $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

        // Start the query
        $userAuth = Auth::user();
        $query = UmpanbalikT::with('rencanakerja', 'answers.question', 'tanggapanUmpanBalik', 'pengawasnama', 'user.sekolah')
            ->whereNotNull('submitted_at')
            ->latest('submitted_at');

        if ($userAuth->role == 'Stakeholder' || $userAuth->role == 'Admin') {
            $query->whereHas('pengawasnama', function($q) use ($userAuth) {
                $q->where('kabupaten_id', $userAuth->kabupaten_id);
            });
        }

        // Apply month filter
        if ($bln !== 'all') {
            $query->whereMonth('submitted_at', $monthNumber);
        }

        // Apply year filter
        if ($tahun !== 'all') {
            $query->whereYear('submitted_at', $tahun);
        }

        // Apply pengawas filter
        if ($pengawas !== 'all') {
             $query->where('id_pengawas', $pengawas);
        }

        if ($kategori !== 'all') {
            if ($kategori === 'rhk3') {
                 $query->whereColumn('id_user', 'id_pengawas');
            } else {
                $query->whereHas('rencanakerja', function ($q) use ($kategori) {
                    $q->where('kategoriprogram_id', $kategori);
                });
            }
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Search by nama_sekolah via relationships
                $q->orWhereHas('user.sekolah', function ($subQuery) use ($search) {
                    $subQuery->where('nama_sekolah', 'like', "%{$search}%");
                });

                // Search by nama_program_kerja
                $q->orWhereHas('rencanakerja', function ($subQuery) use ($search) {
                    $subQuery->where('nama_program_kerja', 'like', "%{$search}%");
                });

                // Search by pengawas name
                $q->orWhereHas('pengawasnama', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }


        // Get the filtered data and map it
        $data = $query->get()->map(function ($row) {
            $fotoBase64 = null;
            // Priority 1: Dynamic Image
            $fileAnswer = $row->answers->first(function($a){
                 return $a->id_question == 13 || optional($a->question)->type_input == 'file';
            });
            
            if ($fileAnswer && !empty($fileAnswer->answer)) {
                $filename = $fileAnswer->answer;
                $path = Storage::disk('shared')->path('umpanbalik_dynamic/' . $filename);
                $fotoBase64 = $this->imageToBase64($path);
            } 
            
            if (!$fotoBase64) {
                 // Priority 2: Legacy Image
                $legacy = $row->tanggapanUmpanBalik->first();
                if ($legacy && !empty($legacy->foto)) {
                    $filename = $legacy->foto;
                    $root = config('filesystems.disks.shared.root');
                    $path = $root . '/umpanbalik/' . $filename;
                    $fotoBase64 = $this->imageToBase64($path);
                }
            }

            // Logic for School Name (safe check)
            $namaSekolah = '-';
            $cariguru = GuruM::find($row->id_user);
            if ($cariguru) {
                 $sekolah = SekolahM::find($cariguru->sekolah_id);
                 $namaSekolah = $sekolah ? $sekolah->nama_sekolah : '-';
            } elseif ($row->id_user == $row->id_pengawas) {
                 $namaSekolah = 'Mandiri (Refleksi Pengawas)';
            }


            return [
                'tanggal' => $row->tgl_pendampingan ? $row->tgl_pendampingan->format('d M Y') : ($row->submitted_at ? $row->submitted_at->format('d M Y') : '-'),
                'foto_base64' => $fotoBase64,
                'nama_sekolah' => $namaSekolah,
                'program' => $row->rencanakerja->nama_program_kerja ?? '-',
                'pengawas' => $row->pengawasnama->name ?? '-',
                'rtl_status' => ($row->is_rtl == 1 ? 'Sudah dilakukan' : 'Belum dilakukan') . ($row->tgl_rtl ? ' (' . $row->tgl_rtl->format('Y-m-d H:i:s') . ')' : ''),
                'catatan_rtl' => $row->catatan_rtl ?? '-',
            ];
        });

        // Generate the PDF using the filtered data
        $pdf = PDF::loadView('dokumentasipendampingan.dokumentasi', ['data' => $data])->setPaper('a4', 'landscape');

        // Return the PDF as a downloadable file
        return $pdf->download('Laporan_Dokumentasi.pdf');
    }

    public function getdatapengawas(Request $request){
        if ($request->ajax()) {

            $pengawas = $request->input('pengawas', 'all');
            $tahun = $request->input('tahun', 'all');
            $bln = $request->input('bln', 'all');
            $kategori = $request->input('kategori', 'all');
            $monthNamesIndo = [
                'Januari' => 1,
                'Februari' => 2,
                'Maret' => 3,
                'April' => 4,
                'Mei' => 5,
                'Juni' => 6,
                'Juli' => 7,
                'Agustus' => 8,
                'September' => 9,
                'Oktober' => 10,
                'November' => 11,
                'Desember' => 12
            ];

            // Cek apakah nama bulan sesuai dengan bulan yang diterima dalam bahasa Indonesia
            $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

            // Change base query to UmpanbalikT to include dynamic submissions
            $post = UmpanbalikT::with('rencanakerja', 'answers.question', 'tanggapanUmpanBalik', 'pengawasnama')
                ->where('id_pengawas', Auth::user()->id)
                ->whereNotNull('submitted_at')
                ->latest('submitted_at');

            // Apply filter for 'bln' (bulan)
            if ($bln !== 'all') {
                $post->whereMonth('submitted_at', $monthNumber);
            }
            if ($tahun !== 'all') {
                $post->whereYear('submitted_at', $tahun);
            }


            if ($pengawas !== 'all') {
                 $post->where('id_pengawas', $pengawas);
            }

            if ($kategori !== 'all') {
                if ($kategori === 'rhk3') {
                     $post->whereColumn('id_user', 'id_pengawas');
                } else {
                    $post->whereHas('rencanakerja', function ($q) use ($kategori) {
                        $q->where('kategoriprogram_id', $kategori);
                    });
                }
            }


            return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('tanggal', function($row){
                    return !empty($row->rencanakerja->created_at) ? $row->rencanakerja->created_at->format('d M Y') : '-';
                })
                ->addColumn('foto', function($row){
                    // Priority 1: Dynamic Image (Q13 or generic file input)
                    $fileAnswer = $row->answers->first(function($a){
                         // Check for Q13 specific or any file input type
                         return $a->id_question == 13 || optional($a->question)->type_input == 'file';
                    });

                    if($fileAnswer && !empty($fileAnswer->answer)){
                         $fotoUrl = route('umpanbalik.dynamic.file', $fileAnswer->answer);
                         return  ' <img src="'.$fotoUrl.'" height="100px" alt="Bukti" class="card-img-top">';
                    }
                    
                    // Priority 2: Legacy Image
                    $legacy = $row->tanggapanUmpanBalik->first();
                    if($legacy && !empty($legacy->foto)){
                        $foto = route('umpanbalikfoto', $legacy->foto);
                        return  ' <img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top">';
                    }

                    return '-';
                })

                ->addColumn('program', function($row){
                    return !empty($row->rencanakerja) ? $row->rencanakerja->nama_program_kerja : '-';
                })
                ->addColumn('pengawas', function($row){
                    return !empty($row->pengawasnama) ? $row->pengawasnama->name : '-';
                })
                ->addColumn('nama_sekolah', function($row) {
                    // Try getting Guru -> Sekolah
                    $cariguru = GuruM::find($row->id_user);
                    if ($cariguru) {
                         $sekolah = SekolahM::find($cariguru->sekolah_id);
                         return $sekolah ? $sekolah->nama_sekolah : '-';
                    }
                    
                    // Fallback: Check if user is the supervisor (Self-Reflection)
                    if ($row->id_user == $row->id_pengawas) {
                         return 'Mandiri (Refleksi Pengawas)';
                    }
                    
                    return '-';
                })
                ->addColumn('rtl_status', function($row) {
                    $rtlStatus = $row->is_rtl == 1 ? 'Sudah dilakukan' : 'Belum dilakukan';
                    $rtlDate = $row->tgl_rtl ? ' (' . $row->tgl_rtl->format('Y-m-d H:i:s') . ')' : '';
                    return $rtlStatus . $rtlDate;
                })
                ->addColumn('catatan_rtl', function($row) {
                    return $row->catatan_rtl ?? '-';
                })
                ->rawColumns(['tanggal', 'foto', 'program', 'pengawas', 'nama_sekolah', 'rtl_status', 'catatan_rtl'])
                ->make(true);
        }
    }

    public function exportPDFPengawas(Request $request)
    {
        // Retrieve filter values from the request
        $pengawas = $request->input('pengawas', 'all');
        $tahun = $request->input('tahun', 'all');
        $bln = $request->input('bln', 'all');
        $search = $request->input('search', '');
        $kategori = $request->input('kategori', 'all');

        // Define month names mapping for Indonesian months
        $monthNamesIndo = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        // Convert the month name to the corresponding number, or use 'all'
        $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

        // Start the query: Query UmpanbalikT directly
        $query = UmpanbalikT::with('rencanakerja', 'answers.question', 'tanggapanUmpanBalik', 'pengawasnama')
            ->where('id_pengawas', Auth::user()->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at');


        // Apply month filter
        if ($bln !== 'all') {
            $query->whereMonth('submitted_at', $monthNumber);
        }

        // Apply year filter
        if ($tahun !== 'all') {
            $query->whereYear('submitted_at', $tahun);
        }

        // Apply pengawas filter
        if ($pengawas !== 'all') {
             $query->where('id_pengawas', $pengawas);
        }

        if ($kategori !== 'all') {
            if ($kategori === 'rhk3') {
                 $query->whereColumn('id_user', 'id_pengawas');
            } else {
                $query->whereHas('rencanakerja', function ($q) use ($kategori) {
                    $q->where('kategoriprogram_id', $kategori);
                });
            }
        }

        // Apply search filter (Updated for UmpanbalikT relations)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Search by nama_sekolah via relationships (Guru check)
                $q->orWhereHas('user.sekolah', function ($subQuery) use ($search) {
                    $subQuery->where('nama_sekolah', 'like', "%{$search}%");
                });

                // Search by nama_program_kerja
                $q->orWhereHas('rencanakerja', function ($subQuery) use ($search) {
                    $subQuery->where('nama_program_kerja', 'like', "%{$search}%");
                });

                // Search by pengawas name
                $q->orWhereHas('pengawasnama', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }


        // Get the filtered data and map it
        $data = $query->get()->map(function ($row) {
            
            $fotoBase64 = null;
            // Priority 1: Dynamic Image
            $fileAnswer = $row->answers->first(function($a){
                 return $a->id_question == 13 || optional($a->question)->type_input == 'file';
            });
            
            if ($fileAnswer && !empty($fileAnswer->answer)) {
                $filename = $fileAnswer->answer;
                $path = Storage::disk('shared')->path('umpanbalik_dynamic/' . $filename);
                $fotoBase64 = $this->imageToBase64($path);
            } 
            
            if (!$fotoBase64) {
                 // Priority 2: Legacy Image
                $legacy = $row->tanggapanUmpanBalik->first();
                if ($legacy && !empty($legacy->foto)) {
                    $filename = $legacy->foto;
                    $root = config('filesystems.disks.shared.root');
                    $path = $root . '/umpanbalik/' . $filename;
                    $fotoBase64 = $this->imageToBase64($path);
                }
            }
            
            // Logic for School Name (safe check)
            $namaSekolah = '-';
            $cariguru = GuruM::find($row->id_user);
            if ($cariguru) {
                 $sekolah = SekolahM::find($cariguru->sekolah_id);
                 $namaSekolah = $sekolah ? $sekolah->nama_sekolah : '-';
            } elseif ($row->id_user == $row->id_pengawas) {
                 $namaSekolah = 'Mandiri (Refleksi Pengawas)';
            }


            return [
                'tanggal' => $row->tgl_pendampingan ? $row->tgl_pendampingan->format('d M Y') : ($row->submitted_at ? $row->submitted_at->format('d M Y') : '-'),
                'foto_base64' => $fotoBase64,
                'nama_sekolah' => $namaSekolah,
                'program' => $row->rencanakerja->nama_program_kerja ?? '-',
                'pengawas' => $row->pengawasnama->name ?? '-',
                'rtl_status' => ($row->is_rtl == 1 ? 'Sudah dilakukan' : 'Belum dilakukan') . ($row->tgl_rtl ? ' (' . $row->tgl_rtl->format('Y-m-d H:i:s') . ')' : ''),
                'catatan_rtl' => $row->catatan_rtl ?? '-',
            ];
        });

        // Generate the PDF using the filtered data
        // Generate the PDF using the filtered data
        $pdf = PDF::loadView('dashboard_pengawas.umpanbalik.dokumentasi_pdf', [
            'data' => $data,
            'user' => Auth::user()
        ])->setPaper('a4', 'landscape');

        // Return the PDF as a downloadable file
        return $pdf->download('Laporan_Dokumentasi.pdf');
    }

    private function imageToBase64($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }


}
