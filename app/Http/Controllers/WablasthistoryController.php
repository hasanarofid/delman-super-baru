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
use Illuminate\Support\Facades\Http;
use App\Traits\StakeholderAccess;

class WablasthistoryController extends Controller
{
    use StakeholderAccess;
    public function index(){
        return view('wablasthistory.index');
    }

    public function getdata(Request $request) {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = WhatsappMessagesLog::with('rencanakerja', 'kepalasekolah')->latest();

            $query = $this->applyStakeholderFilter($query, 'rencanakerja.pengawasnama.kabupaten_id', null, 'rencanakerja.pengawasnama');

            // Hitung ringkasan statistik (Total, Sudah Kirim, Belum Kirim)
            $countQuery = WhatsappMessagesLog::query();
            $countQuery = $this->applyStakeholderFilter($countQuery, 'rencanakerja.pengawasnama.kabupaten_id', null, 'rencanakerja.pengawasnama');
            $totalCount = (clone $countQuery)->count();
            $sudahCount = (clone $countQuery)->where('is_sent', 1)->count();
            $belumCount = (clone $countQuery)->where('is_sent', 0)->count();

            if ($request->has('status') && $request->status !== 'all') {
                if ($request->status === 'belum_kirim') {
                    $query->where('is_sent', 0);
                } elseif ($request->status === 'sudah_kirim') {
                    $query->where('is_sent', 1);
                }
            }

            // Return data for DataTables
            return Datatables::of($query->get())
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
                })
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
                    $id_sekolah = $row->kepalasekolah->id ?? 0;
                    $is_mandiri = $row->rencanakerja->is_mandiri ?? 0;
                    if ($user && $user->role == 'Super Admin' && ($id_sekolah || $is_mandiri == 1)) {
                        return '<a id="sendWaButton-' . $row->rencana_kerja_id . '" onclick="kirimWaBlast(' . $row->rencana_kerja_id . ','.$id_sekolah.')" class="btn btn-sm bg-success text-white">
                        <i class="fa fa-envelope"></i> Kirim Wa
                    </a>';
                    } else {
                        return '';
                    }
                })
                ->with([
                    'total_count' => $totalCount,
                    'sudah_count' => $sudahCount,
                    'belum_count' => $belumCount,
                ])
                ->rawColumns(['checkbox', 'rencana', 'kepalasekolah', 'status', 'action'])
                ->make(true);
        }

        return view('wablasthistory.index');
    }

    public function kirimMasal(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role != 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $logIds = $request->input('log_ids', []);
        if (empty($logIds) || !is_array($logIds)) {
            return response()->json(['success' => false, 'message' => 'Pilih setidaknya satu data.'], 400);
        }

        $logs = WhatsappMessagesLog::whereIn('id', $logIds)->get();
        $rencanaTugasController = app(RencanaTugasController::class);
        $queuedCount = 0;

        foreach ($logs as $log) {
            if ($log->rencana_kerja_id) {
                try {
                    $id_sekolah = $log->kepala_sekolah_id ?? 0;
                    $res = $rencanaTugasController->kirimWaSekolah($log->rencana_kerja_id, $id_sekolah, $log->id);
                    if ($res instanceof \Illuminate\Http\JsonResponse) {
                        $data = $res->getData(true);
                        if (!empty($data['success'])) {
                            $log->failure_reason = 'Sedang diantri ke WA Blast...';
                            $log->save();
                            $queuedCount++;
                        }
                    } else {
                        $log->failure_reason = 'Sedang diantri ke WA Blast...';
                        $log->save();
                        $queuedCount++;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Bulk send WA error for Log ID {$log->id}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$queuedCount} pesan WA berhasil dimasukkan ke dalam antrean WA Blast."
        ]);
    }
}
