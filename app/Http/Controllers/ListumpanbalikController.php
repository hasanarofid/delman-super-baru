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
use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Add Str for slug generation

class ListumpanbalikController extends Controller
{
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
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }


        return view('listumpanbalik.index',compact(
            'listPengawas',
            'months',
            'currentYear',
            'years'
            ));
    }

    public function indexpengawas(){
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
        for ($i = 0; $i < 12; $i++) {
            $timestamp = strtotime("+$i month");
            $monthNumber = date('n', $timestamp);
            $months[] = [
                'value' => $monthNumber,                // Month number (1-12)
                'name' => $monthNamesIndo[$monthNumber] // Full month name in Indonesian
            ];
        }


        return view('dashboard_pengawas.umpanbalik.list',compact(
            'listPengawas',
            'months',
            'currentYear',
            'years'
        ));
    }


    public function getdata(Request $request){
        if ($request->ajax()) {
            $user = Auth::user();
            $query = UmpanbalikT::with(['rencanakerja', 'category'])->latest();

            if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
                $query->whereHas('pengawasnama', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }

             // Filter berdasarkan pengawas
            if ($request->has('pengawas') && $request->pengawas !== 'all') {
                $query->whereHas('rencanakerja', function ($q) use ($request) {
                    $q->where('id_pengawas', $request->pengawas);
                });
            }

            // Filter berdasarkan bulan
            if ($request->has('bln') && $request->bln !== 'all') {
                $query->whereHas('rencanakerja', function ($q) use ($request) {
                    $q->where('bulan', $request->bln);
                });
            }

            // Filter berdasarkan tahun ajaran
            if ($request->has('tahun') && $request->tahun !== 'all') {
                $query->whereHas('rencanakerja', function ($q) use ($request) {
                    $q->where('tahun_ajaran', $request->tahun);
                });
            }


            return Datatables::of($query->get())
                       ->addIndexColumn()
                       ->addColumn('tanggal', function($row){
                        return $row->created_at->format('d M Y h:i:s');
                    })
                    ->addColumn('pengawas', function($row){
                        $user = User::where('id',$row->id_pengawas)->first();
                        return $user->nip.' - '.$user->name;
                    })
                    ->addColumn('kepala_sekolah', function($row){
                        $cariguru = GuruM::findorFail($row->id_user);
                        return $cariguru->nama;
                    })
                    ->addColumn('sasaran', function($row){
                        $rencana = RencanaKerjaT::find($row->id_pelaporan);
                        return !empty($rencana) ? $rencana->nama_program_kerja : '-';
                    })
                    ->addColumn('tanggapan_status', function($row){
                        $tanggapan = TanggapanUmpanbalikT::where('id_umpanbalik',$row->id)->first();
                        if($tanggapan || $row->submitted_at !== null){
                            $btn = '<span class="badge bg-label-success m-1" > Sudah diberi tanggapan </span>';
                        }else{
                            $btn = '<span class="badge bg-label-danger m-1" > Belum diberi tanggapan </span>';
                        }
                        return $btn;

                    })
                    ->addColumn('is_rtl', function($row){
                        return $row->is_rtl;
                    })
                    ->addColumn('kategori', function($row){ // Add this column
                        if ($row->id_category == 0) {
                            return 'Default Feedback (Statis)';
                        } else {
                            return $row->category->name ?? 'N/A';
                        }
                    })
                    ->addColumn('tgl_rtl', function($row){
                        return $row->is_rtl == 1 && $row->tgl_rtl ? Carbon::parse($row->tgl_rtl)->format('d M Y H:i:s') : '';
                    })
                    ->addColumn('nama_sekolah', function($row) {
                        $cariguru = GuruM::findorFail($row->id_user);
                        $sekolahs = SekolahM::findorFail($cariguru->sekolah_id);
                        return $sekolahs->nama_sekolah;
                    })
                       ->addColumn('action', function($row){
                            $tanggapan = TanggapanUmpanbalikT::where('id_umpanbalik', $row->id)->first();

                            if ($row->id_category == 0) {
                                $fullUrl = url('umpan-balik-view/' . $row->generate_url);
                            } else {
                                $categorySlug = $row->category ? Str::slug($row->category->name) : 'default';
                                $fullUrl = route('superadmin.dynamic.umpanbalik.view', ['category_slug' => $categorySlug, 'generate_url' => $row->generate_url]);
                            }

                            // If 'Belum diberi tanggapan', disable the button
                            if (!$tanggapan && $row->submitted_at === null) {
                                $btn = '<a href="#" class="btn btn-sm bg-warning text-white disabled" style="pointer-events: none;" > <i class="fa fa-eye"></i> Belum diberi tanggapan</a>';
                            } else {
                                $btn = '<a target="_blank" href="'.$fullUrl.'" class="btn btn-sm bg-primary text-white" > <i class="fa fa-eye"></i> View</a>';
                            }
                            return $btn;
                       })
                       ->rawColumns(['action','sasaran','kepala_sekolah','nama_sekolah','tanggal','pengawas','tanggapan_status','is_rtl','tgl_rtl','kategori']) // Add 'kategori' here
                       ->make(true);
           }
    }

    public function getdatapengawas(Request $request){
        if ($request->ajax()) {
            $query = UmpanbalikT::with(['rencanakerja', 'category'])
            ->where('umpanbalik_t.id_pengawas', Auth::user()->id)
            ->latest();


           // Filter berdasarkan bulan
           if ($request->has('bln') && $request->bln !== 'all') {
               $query->whereHas('rencanakerja', function ($q) use ($request) {
                   $q->where('bulan', $request->bln);
               });
           }

           // Filter berdasarkan tahun ajaran
           if ($request->has('tahun') && $request->tahun !== 'all') {
               $query->whereHas('rencanakerja', function ($q) use ($request) {
                   $q->where('tahun_ajaran', $request->tahun);
               });
           }


            return Datatables::of($query->get())
                       ->addIndexColumn()
                       ->addColumn('tanggal', function($row){
                        return $row->created_at->format('d M Y h:i:s');
                    })
                    ->addColumn('pengawas', function($row){
                        $user = User::where('id',$row->id_pengawas)->first();
                        return $user->nip.' - '.$user->name;
                    })
                    ->addColumn('kepala_sekolah', function($row){
                        $cariguru = GuruM::findorFail($row->id_user);
                        return $cariguru->nama;
                    })
                    ->addColumn('sasaran', function($row){
                        $rencana = RencanaKerjaT::find($row->id_pelaporan);
                        return !empty($rencana) ? $rencana->nama_program_kerja : '-';
                    })
                    ->addColumn('tanggapan_status', function($row){
                        $tanggapan = TanggapanUmpanbalikT::where('id_umpanbalik',$row->id)->first();
                        if($tanggapan || $row->submitted_at !== null){
                            $btn = '<span class="badge bg-label-success m-1" > Sudah diberi tanggapan </span>';
                        }else{
                            $btn = '<span class="badge bg-label-danger m-1" > Belum diberi tanggapan </span>';
                        }
                        return $btn;

                    })
                    ->addColumn('is_rtl', function($row){
                        return $row->is_rtl;
                    })
                    ->addColumn('kategori', function($row){ // Add this column
                        if ($row->id_category == 0) {
                            return 'Default Feedback (Statis)';
                        } else {
                            return $row->category->name ?? 'N/A';
                        }
                    })
                    ->addColumn('tgl_rtl', function($row){
                        return $row->is_rtl == 1 && $row->tgl_rtl ? Carbon::parse($row->tgl_rtl)->format('d M Y H:i:s') : '';
                    })
                    ->addColumn('nama_sekolah', function($row) {
                        $cariguru = GuruM::findorFail($row->id_user);
                        $sekolahs = SekolahM::findorFail($cariguru->sekolah_id);
                        return $sekolahs->nama_sekolah;
                    })
                       ->addColumn('action', function($row){
                            $tanggapan = TanggapanUmpanbalikT::where('id_umpanbalik', $row->id)->first();

                            if ($row->id_category == 0) {
                                $fullUrl = url('umpan-balik-view/' . $row->generate_url);
                            } else {
                                $categorySlug = $row->category ? Str::slug($row->category->name) : 'default';
                                $fullUrl = route('superadmin.dynamic.umpanbalik.view', ['category_slug' => $categorySlug, 'generate_url' => $row->generate_url]);
                            }

                            if (!$tanggapan) {
                                $btn = '<a href="#" class="btn btn-sm bg-warning text-white disabled" style="pointer-events: none;" > <i class="fa fa-eye"></i> Belum diberi tanggapan</a>';
                            } else {
                                $btn = '<a target="_blank" href="'.$fullUrl.'" class="btn btn-sm bg-primary text-white" > <i class="fa fa-eye"></i> View</a>';
                            }
                            return $btn;
                       })

                       ->rawColumns(['action','sasaran','kepala_sekolah','nama_sekolah','tanggal','pengawas','tanggapan_status','is_rtl','tgl_rtl','kategori']) // Add 'kategori' here
                       ->make(true);
           }
    }

    public function updateRTL(Request $request)
    {
        Log::info('Update RTL Request:', $request->all());
        $umpanbalik = UmpanbalikT::find($request->id);
        if ($umpanbalik) {
            $umpanbalik->is_rtl = $request->is_rtl;
            $umpanbalik->tgl_rtl = now(); // Set current date and time
            $umpanbalik->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

}
