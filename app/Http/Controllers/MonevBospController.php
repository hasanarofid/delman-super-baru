<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MonevBosp;
use App\Models\SekolahbinaanT;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonevBospExport;

class MonevBospController extends Controller
{
    public function index(Request $request)
    {
        $pengawasId = Auth::user()->id;
        
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');

        $query = MonevBosp::with('sekolah')
            ->where('pengawas_id', $pengawasId);

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        $monevList = $query->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        // Rekapitulasi Widgets
        $totalSekolahDimonev = $monevList->count();
        $totalSiswaRiil = $monevList->sum('total_siswa_riil');
        $sekolahSelisih = $monevList->filter(function($item) {
            return $item->total_siswa_riil != $item->siswa_dinas_bos;
        })->count();
        
        $rataSerapan = $monevList->avg('realisasi_bosp') ?? 0;

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('pengawas.monev_bosp.index', compact(
            'monevList', 'year', 'month', 'bulanOptions', 
            'totalSekolahDimonev', 'totalSiswaRiil', 'sekolahSelisih', 'rataSerapan'
        ));
    }

    public function create()
    {
        $pengawasId = Auth::user()->id;
        
        $sekolahBinaan = SekolahbinaanT::with(['sekolah.kabupaten'])
            ->where('id_pengawas', $pengawasId)
            ->get();

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tahunSekarang = date('Y');

        return view('pengawas.monev_bosp.create', compact('sekolahBinaan', 'bulanOptions', 'tahunSekarang'));
    }

    public function store(Request $request)
    {
        $pengawasId = Auth::user()->id;
        
        $request->validate([
            'sekolah_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required|numeric',
            'siswa_kelas_10' => 'required|numeric',
            'siswa_kelas_11' => 'required|numeric',
            'siswa_kelas_12' => 'required|numeric',
            'siswa_dinas_bos' => 'required|numeric',
            'file_sptjm' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // 2MB limit
        ]);

        $exists = MonevBosp::where('pengawas_id', $pengawasId)
            ->where('sekolah_id', $request->sekolah_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Laporan Monev BOSP untuk sekolah dan bulan tersebut sudah pernah dibuat.');
        }

        $data = $request->except(['_token', 'file_sptjm']);
        $data['pengawas_id'] = $pengawasId;
        $data['total_siswa_riil'] = $request->siswa_kelas_10 + $request->siswa_kelas_11 + $request->siswa_kelas_12;

        if ($request->hasFile('file_sptjm')) {
            $file = $request->file('file_sptjm');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sptjm'), $fileName);
            $data['file_sptjm'] = $fileName;
        }

        MonevBosp::create($data);

        return redirect()->route('pengawas.monev-bosp.index')->with('success', 'Laporan Monev BOSP berhasil disimpan.');
    }
    
    public function edit($id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBosp::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        $sekolahBinaan = SekolahbinaanT::with(['sekolah.kabupaten'])
            ->where('id_pengawas', $pengawasId)
            ->get();

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tahunSekarang = date('Y');

        return view('pengawas.monev_bosp.edit', compact('monev', 'sekolahBinaan', 'bulanOptions', 'tahunSekarang'));
    }
    
    public function update(Request $request, $id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBosp::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        $request->validate([
            'sekolah_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required|numeric',
            'siswa_kelas_10' => 'required|numeric',
            'siswa_kelas_11' => 'required|numeric',
            'siswa_kelas_12' => 'required|numeric',
            'siswa_dinas_bos' => 'required|numeric',
            'file_sptjm' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // 2MB limit
        ]);

        $exists = MonevBosp::where('pengawas_id', $pengawasId)
            ->where('sekolah_id', $request->sekolah_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Laporan Monev BOSP untuk sekolah dan bulan tersebut sudah pernah dibuat.');
        }

        $data = $request->except(['_token', '_method', 'file_sptjm']);
        $data['total_siswa_riil'] = $request->siswa_kelas_10 + $request->siswa_kelas_11 + $request->siswa_kelas_12;

        if ($request->hasFile('file_sptjm')) {
            $file = $request->file('file_sptjm');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sptjm'), $fileName);
            $data['file_sptjm'] = $fileName;
            
            // Delete old file if exists
            if ($monev->file_sptjm && file_exists(public_path('uploads/sptjm/' . $monev->file_sptjm))) {
                @unlink(public_path('uploads/sptjm/' . $monev->file_sptjm));
            }
        }

        $monev->update($data);

        return redirect()->route('pengawas.monev-bosp.index')->with('success', 'Laporan Monev BOSP berhasil diupdate.');
    }
    
    public function destroy($id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBosp::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        if ($monev->file_sptjm && file_exists(public_path('uploads/sptjm/' . $monev->file_sptjm))) {
            @unlink(public_path('uploads/sptjm/' . $monev->file_sptjm));
        }
            
        $monev->delete();
        
        return redirect()->route('pengawas.monev-bosp.index')->with('success', 'Laporan Monev BOSP berhasil dihapus.');
    }

    public function export()
    {
        $pengawasId = Auth::user()->id;
        $timestamp = date('Y-m-d_H-i-s');
        return Excel::download(new MonevBospExport($pengawasId), 'Laporan_Monev_BOSP_' . $timestamp . '.xlsx');
    }
    
    public function exportExcel(Request $request)
    {
        $pengawasId = Auth::user()->id;
        $year = $request->input('tahun', date('Y'));
        $month = $request->input('bulan', 'all');

        $query = MonevBosp::with('sekolah')->where('pengawas_id', $pengawasId);

        if ($year !== 'all') {
            $query->where('tahun', $year);
        }

        if ($month !== 'all') {
            $query->where('bulan', $month);
        }

        $monevList = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        $filename = "rekap_monev_bosp_" . date('Ymd') . ".csv";
        $handle = fopen(public_path($filename), 'w+');
        fputcsv($handle, ['No', 'Nama Sekolah', 'Bulan', 'Tahun', 'Siswa Riil', 'Siswa Dinas', 'Status Data', 'Realisasi BOSP (Rp)', 'Catatan']);

        foreach($monevList as $index => $row) {
            $status = 'Sesuai';
            if ($row->total_siswa_riil > $row->siswa_dinas_bos) $status = 'Lebih';
            elseif ($row->total_siswa_riil < $row->siswa_dinas_bos) $status = 'Kurang';

            fputcsv($handle, [
                $index + 1,
                $row->sekolah->nama_sekolah ?? '-',
                $row->bulan,
                $row->tahun,
                $row->total_siswa_riil,
                $row->siswa_dinas_bos,
                $status,
                $row->realisasi_bosp,
                $row->catatan_observasi
            ]);
        }
        fclose($handle);

        return response()->download(public_path($filename))->deleteFileAfterSend(true);
    }
}
