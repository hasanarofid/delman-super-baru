<?php

namespace App\Http\Controllers;

use App\Models\UmpanbalikT;
use Illuminate\Http\Request;
use App\Models\RencanaKerjaT;
use App\Kabupaten;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Imports\ImportUser;
use App\Exports\ExportUser;
use App\Models\WhatsappMessagesLog;
use App\SekolahM;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class WablasthistoryController extends Controller
{
    public function index(){
        return view('wablasthistory.index');
    }

    public function getdata(Request $request) {
        if ($request->ajax()) {
            $user = Auth::user();
            // Base query with eager loading
            $query = WhatsappMessagesLog::with('rencanakerja', 'kepalasekolah')->latest();
    
            if ($user->role == 'Stakeholder' || $user->role == 'Admin') {
                $query->whereHas('rencanakerja.pengawasnama', function($q) use ($user) {
                    $q->where('kabupaten_id', $user->kabupaten_id);
                });
            }
    
            // Return data for DataTables
            return Datatables::of($query->get())
                ->addIndexColumn()
                ->addColumn('rencana', function($row) {
                    return $row->rencanakerja->nama_program_kerja ?? '-';
                })
                ->addColumn('kepalasekolah', function($row) {
                    return $row->kepalasekolah->nama ?? '-';
                })
             
                ->addColumn('status', function($row) {
                    return $row->is_sent == 1
                        ? '<span class="badge bg-label-success m-1">Sudah Kirim WA Blast</span>'
                        : '<span class="badge bg-label-danger m-1">Belum Kirim WA Blast</span>';
                })
                ->addColumn('action', function($row) {
                    $user = Auth::user();
                    $id_sekolah = $row->kepalasekolah->id ?? null;
                    if ($user && $user->role == 'Super Admin' && $id_sekolah) {
                        return '<a id="sendWaButton-' . $row->rencana_kerja_id . '" onclick="kirimWaBlast(' . $row->rencana_kerja_id . ','.$id_sekolah.')" class="btn btn-sm bg-success text-white">
                        <i class="fa fa-envelope"></i> Kirim Wa
                    </a>';
                    } else {
                        return ''; // Tidak menampilkan tombol aksi jika bukan Super Admin
                    }
                    
                })
                ->rawColumns(['rencana', 'kepalasekolah', 'status', 'action'])
                ->make(true);
        }
    
        return view('rencanakerja.index');
    }
}
