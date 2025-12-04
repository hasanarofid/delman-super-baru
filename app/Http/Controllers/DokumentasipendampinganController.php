<?php

namespace App\Http\Controllers;

use App\GuruM;
use App\Models\RencanaKerjaT;
use App\Models\UmpanbalikT;
use App\SekolahM;
use App\TanggapanUmpanbalikT;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade as PDF;
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
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }

        $listPengawas = User::where('role','pengawas')->get();

        return view('dashboard_pengawas.umpanbalik.dokumentasi',compact('listPengawas',
        'months',
        'currentYear',
        'years'
    ));
    }




    public function index(){

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

        $listPengawas = User::where('role','pengawas')->get();

        return view('dokumentasipendampingan.index',compact('listPengawas',
        'months',
        'currentYear',
        'years'
    ));
    }

    public function getdata(Request $request){
        if ($request->ajax()) {

            $pengawas = $request->input('pengawas', 'all');
            $tahun = $request->input('tahun', 'all');
            $bln = $request->input('bln', 'all');
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

            $post = TanggapanUmpanbalikT::with('umpanBalikT','umpanBalikT.rencanakerja')->latest();
             // Apply filter for 'bln' (bulan)
             if ($bln !== 'all') {
                $post->whereMonth('created_at', $monthNumber);
            }
            if ($tahun !== 'all') {
                $post->whereYear('created_at', $tahun);
            }


            $post->whereHas('umpanBalikT', function ($q) use ($pengawas) {
                if ($pengawas !== 'all') {
                    $q->where('id_pengawas', $pengawas);
                }
            });


                return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('tanggal', function($row){
                    return !empty($row->umpanBalikT->rencanakerja->created_at) ? $row->umpanBalikT->rencanakerja->created_at->format('d M Y') : '-';
                })
                ->addColumn('foto', function($row){
                    if(!empty($row->foto)){
                        $foto = route('umpanbalikfoto', $row->foto);

                        return  ' <img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top">';
                    }else{
                        return  '-';
                    }

                })

                ->addColumn('program', function($row){
                    return !empty($row->umpanBalikT->rencanakerja) ? $row->umpanBalikT->rencanakerja->nama_program_kerja : '-';
                })
                ->addColumn('pengawas', function($row){
                    return !empty($row->umpanBalikT->rencanakerja) ? $row->umpanBalikT->pengawasnama->name : '-';
                })
                ->addColumn('nama_sekolah', function($row) {
                    $cariguru = GuruM::findOrFail($row->umpanBalikT->id_user);
                    $sekolahs = SekolahM::findOrFail($cariguru->sekolah_id);
                    return $sekolahs->nama_sekolah;
                })
                ->rawColumns(['tanggal', 'foto', 'program', 'pengawas', 'nama_sekolah'])
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

    // Define month names mapping for Indonesian months
    $monthNamesIndo = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
        'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
    ];

    // Convert the month name to the corresponding number, or use 'all'
    $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

    // Start the query
    $query = TanggapanUmpanbalikT::with('umpanBalikT', 'umpanBalikT.rencanakerja', 'umpanBalikT.pengawasnama')->latest();

    // Apply month filter
    if ($bln !== 'all') {
        $query->whereMonth('created_at', $monthNumber);
    }

    // Apply year filter
    if ($tahun !== 'all') {
        $query->whereYear('created_at', $tahun);
    }

    // Apply pengawas filter
    if ($pengawas !== 'all') {
        $query->whereHas('umpanBalikT', function ($q) use ($pengawas) {
            $q->where('id_pengawas', $pengawas);
        });
    }

    // Apply search filter
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            // Search by nama_sekolah via relationships
            $q->orWhereHas('umpanBalikT.user.sekolah', function ($subQuery) use ($search) {
                $subQuery->where('nama_sekolah', 'like', "%{$search}%");
            });

            // Search by nama_program_kerja
            $q->orWhereHas('umpanBalikT.rencanakerja', function ($subQuery) use ($search) {
                $subQuery->where('nama_program_kerja', 'like', "%{$search}%");
            });

            // Search by pengawas name
            $q->orWhereHas('umpanBalikT.pengawasnama', function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%");
            });
        });
    }


    // Get the filtered data and map it
    $data = $query->get()->map(function ($row) {
        return [
            'tanggal' => $row->created_at->format('d M Y'),
            'foto' => $row->foto,
            'nama_sekolah' => optional(SekolahM::find(optional(GuruM::find($row->umpanBalikT->id_user))->sekolah_id))->nama_sekolah ?? '-',
            'program' => $row->umpanBalikT->rencanakerja->nama_program_kerja ?? '-',
            'pengawas' => $row->umpanBalikT->pengawasnama->name ?? '-',
        ];
    });

    // Generate the PDF using the filtered data
    $pdf = PDF::loadView('dokumentasipendampingan.dokumentasi', ['data' => $data]);

    // Return the PDF as a downloadable file
    return $pdf->download('Laporan_Dokumentasi.pdf');
}




    public function getdatapengawas(Request $request){
        if ($request->ajax()) {

            $pengawas = $request->input('pengawas', 'all');
            $tahun = $request->input('tahun', 'all');
            $bln = $request->input('bln', 'all');
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

            $post = TanggapanUmpanbalikT::
            with('umpanBalikT','umpanBalikT.rencanakerja')
            ->whereHas('umpanBalikT', function ($query) {
                $query->where('id_pengawas', Auth::user()->id);
            })
            ->latest();
             // Apply filter for 'bln' (bulan)
             if ($bln !== 'all') {
                $post->whereMonth('created_at', $monthNumber);
            }
            if ($tahun !== 'all') {
                $post->whereYear('created_at', $tahun);
            }


            $post->whereHas('umpanBalikT', function ($q) use ($pengawas) {
                if ($pengawas !== 'all') {
                    $q->where('id_pengawas', $pengawas);
                }
            });


                return Datatables::of($post->get())
                ->addIndexColumn()
                ->addColumn('tanggal', function($row){
                    return !empty($row->umpanBalikT->rencanakerja->created_at) ? $row->umpanBalikT->rencanakerja->created_at->format('d M Y') : '-';
                })
                ->addColumn('foto', function($row){
                    if(!empty($row->foto)){
                        $foto = route('umpanbalikfoto', $row->foto);

                        return  ' <img src="'.$foto.'" height="100px" alt="Image placeholder" class="card-img-top">';
                    }else{
                        return  '-';
                    }

                })

                ->addColumn('program', function($row){
                    return !empty($row->umpanBalikT->rencanakerja) ? $row->umpanBalikT->rencanakerja->nama_program_kerja : '-';
                })
                ->addColumn('pengawas', function($row){
                    return !empty($row->umpanBalikT->rencanakerja) ? $row->umpanBalikT->pengawasnama->name : '-';
                })
                ->addColumn('nama_sekolah', function($row) {
                    $cariguru = GuruM::findOrFail($row->umpanBalikT->id_user);
                    $sekolahs = SekolahM::findOrFail($cariguru->sekolah_id);
                    return $sekolahs->nama_sekolah;
                })
                ->rawColumns(['tanggal', 'foto', 'program', 'pengawas', 'nama_sekolah'])
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

        // Define month names mapping for Indonesian months
        $monthNamesIndo = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        // Convert the month name to the corresponding number, or use 'all'
        $monthNumber = isset($monthNamesIndo[$bln]) ? $monthNamesIndo[$bln] : 'all';

        // Start the query
        $query = TanggapanUmpanbalikT::with('umpanBalikT', 'umpanBalikT.rencanakerja', 'umpanBalikT.pengawasnama')
        ->whereHas('umpanBalikT', function ($query) {
            $query->where('id_pengawas', Auth::user()->id);
        })->latest();

        // Apply month filter
        if ($bln !== 'all') {
            $query->whereMonth('created_at', $monthNumber);
        }

        // Apply year filter
        if ($tahun !== 'all') {
            $query->whereYear('created_at', $tahun);
        }

        // Apply pengawas filter
        if ($pengawas !== 'all') {
            $query->whereHas('umpanBalikT', function ($q) use ($pengawas) {
                $q->where('id_pengawas', $pengawas);
            });
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                // Search by nama_sekolah via relationships
                $q->orWhereHas('umpanBalikT.user.sekolah', function ($subQuery) use ($search) {
                    $subQuery->where('nama_sekolah', 'like', "%{$search}%");
                });

                // Search by nama_program_kerja
                $q->orWhereHas('umpanBalikT.rencanakerja', function ($subQuery) use ($search) {
                    $subQuery->where('nama_program_kerja', 'like', "%{$search}%");
                });

                // Search by pengawas name
                $q->orWhereHas('umpanBalikT.pengawasnama', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }


        // Get the filtered data and map it
        $data = $query->get()->map(function ($row) {
            return [
                'tanggal' => $row->created_at->format('d M Y'),
                'foto' => $row->foto,
                'nama_sekolah' => optional(SekolahM::find(optional(GuruM::find($row->umpanBalikT->id_user))->sekolah_id))->nama_sekolah ?? '-',
                'program' => $row->umpanBalikT->rencanakerja->nama_program_kerja ?? '-',
                'pengawas' => $row->umpanBalikT->pengawasnama->name ?? '-',
            ];
        });

        // Generate the PDF using the filtered data
        $pdf = PDF::loadView('dashboard_pengawas.umpanbalik.dokumentasi_pdf', ['data' => $data]);

        // Return the PDF as a downloadable file
        return $pdf->download('Laporan_Dokumentasi.pdf');
    }


}
