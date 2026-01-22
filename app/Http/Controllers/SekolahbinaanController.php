<?php

namespace App\Http\Controllers;

use App\Models\SekolahbinaanT;
use Illuminate\Http\Request;
use Auth;

class SekolahbinaanController extends Controller
{
    //index
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SekolahbinaanT::with(['sekolah', 'sekolah.kepalaSekolahSatu'])
                ->where('id_pengawas', Auth::user()->id)
                ->get();

            return \DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_sekolah', function ($row) {
                    return $row->sekolah->nama_sekolah ?? '-';
                })
                ->addColumn('npsn', function ($row) {
                    return $row->sekolah->npsn ?? '-';
                })
                ->addColumn('nama_kepala_sekolah', function ($row) {
                    return $row->sekolah->kepalaSekolahSatu->nama ?? '-';
                })
                ->addColumn('no_telp', function ($row) {
                    return $row->sekolah->kepalaSekolahSatu->no_telp ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="edit btn btn-primary btn-sm edit-btn" 
                                data-id="' . $row->id_sekolah . '" 
                                data-npsn="' . ($row->sekolah->npsn ?? '') . '" 
                                data-kepala="' . ($row->sekolah->kepalaSekolahSatu->nama ?? '') . '" 
                                data-telp="' . ($row->sekolah->kepalaSekolahSatu->no_telp ?? '') . '">Edit</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard_pengawas.sekolahbinaan.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_sekolah' => 'required',
            'npsn' => 'required',
            'nama_kepala_sekolah' => 'required',
            'no_telp' => 'required',
        ]);

        // Update Sekolah NPSN
        $sekolah = \App\SekolahM::find($request->id_sekolah);
        if ($sekolah) {
            $sekolah->npsn = $request->npsn;
            $sekolah->save();

            // Update or Create Kepala Sekolah in GuruM
            $kepala = \App\GuruM::where('sekolah_id', $request->id_sekolah)
                ->where('jabatan', 'Kepala Sekolah')
                ->first();

            if ($kepala) {
                $kepala->nama = $request->nama_kepala_sekolah;
                $kepala->no_telp = $request->no_telp;
                $kepala->save();
            } else {
                \App\GuruM::create([
                    'sekolah_id' => $request->id_sekolah,
                    'nama' => $request->nama_kepala_sekolah,
                    'no_telp' => $request->no_telp,
                    'jabatan' => 'Kepala Sekolah',
                    'is_aktif' => true
                ]);
            }

            return response()->json(['success' => 'Data berhasil diupdate']);
        }

        return response()->json(['error' => 'Sekolah tidak ditemukan'], 404);
    }
}
