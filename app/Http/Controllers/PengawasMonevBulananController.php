<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MonevBulanan;
use App\Models\SekolahbinaanT;
use Carbon\Carbon;

class PengawasMonevBulananController extends Controller
{
    public function index()
    {
        $pengawasId = Auth::user()->id;
        $monevList = MonevBulanan::with('sekolah')
            ->where('pengawas_id', $pengawasId)
            ->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pengawas.monev_bulanan.index', compact('monevList'));
    }

    public function create()
    {
        $pengawasId = Auth::user()->id;
        
        // Ambil daftar sekolah binaan pengawas ini
        $sekolahBinaan = SekolahbinaanT::with('sekolah')
            ->where('id_pengawas', $pengawasId)
            ->get();

        // Bulan options
        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tahunSekarang = date('Y');

        return view('pengawas.monev_bulanan.create', compact('sekolahBinaan', 'bulanOptions', 'tahunSekarang'));
    }

    public function store(Request $request)
    {
        $pengawasId = Auth::user()->id;
        
        $request->validate([
            'sekolah_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required|numeric'
        ]);

        // Cek apakah laporan untuk sekolah dan bulan ini sudah ada
        $exists = MonevBulanan::where('pengawas_id', $pengawasId)
            ->where('sekolah_id', $request->sekolah_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Laporan Monev untuk sekolah dan bulan tersebut sudah pernah dibuat.');
        }

        $data = $request->except(['_token']);
        $data['pengawas_id'] = $pengawasId;
        
        // Handling empty values for numeric fields
        foreach ($data as $key => $value) {
            if ($value === null && in_array($key, [
                'total_mou', 'jumlah_prestasi', 'serapan_bosp', 'siswa_do', 'siswa_mutasi', 'siswa_pindahan',
                'sarpras_kelas_baik', 'sarpras_kelas_rr', 'sarpras_kelas_rs', 'sarpras_kelas_rb',
                'sarpras_rps_baik', 'sarpras_rps_rr', 'sarpras_rps_rs', 'sarpras_rps_rb',
                'sarpras_lab_baik', 'sarpras_lab_rr', 'sarpras_lab_rs', 'sarpras_lab_rb',
                'sarpras_perpus_baik', 'sarpras_perpus_rr', 'sarpras_perpus_rs', 'sarpras_perpus_rb',
                'mou_kurikulum', 'mou_guru', 'mou_murid', 'mou_sertifikasi', 'mou_rekrutmen', 'mou_csr',
                'lulusan_kerja', 'lulusan_kuliah', 'lulusan_wirausaha',
                'guru_sertifikat', 'guru_non_linier'
            ])) {
                $data[$key] = 0;
            }
        }

        MonevBulanan::create($data);

        return redirect()->route('pengawas.monev-bulanan.index')->with('success', 'Laporan Monev Bulanan berhasil disimpan.');
    }

    public function edit($id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBulanan::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        $sekolahBinaan = SekolahbinaanT::with('sekolah')
            ->where('id_pengawas', $pengawasId)
            ->get();

        $bulanOptions = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tahunSekarang = date('Y');

        return view('pengawas.monev_bulanan.edit', compact('monev', 'sekolahBinaan', 'bulanOptions', 'tahunSekarang'));
    }

    public function update(Request $request, $id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBulanan::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        $request->validate([
            'sekolah_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required|numeric'
        ]);

        $exists = MonevBulanan::where('pengawas_id', $pengawasId)
            ->where('sekolah_id', $request->sekolah_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Laporan Monev untuk sekolah dan bulan tersebut sudah pernah dibuat.');
        }

        $data = $request->except(['_token', '_method']);
        
        foreach ($data as $key => $value) {
            if ($value === null && in_array($key, [
                'total_mou', 'jumlah_prestasi', 'serapan_bosp', 'siswa_do', 'siswa_mutasi', 'siswa_pindahan',
                'sarpras_kelas_baik', 'sarpras_kelas_rr', 'sarpras_kelas_rs', 'sarpras_kelas_rb',
                'sarpras_rps_baik', 'sarpras_rps_rr', 'sarpras_rps_rs', 'sarpras_rps_rb',
                'sarpras_lab_baik', 'sarpras_lab_rr', 'sarpras_lab_rs', 'sarpras_lab_rb',
                'sarpras_perpus_baik', 'sarpras_perpus_rr', 'sarpras_perpus_rs', 'sarpras_perpus_rb',
                'mou_kurikulum', 'mou_guru', 'mou_murid', 'mou_sertifikasi', 'mou_rekrutmen', 'mou_csr',
                'lulusan_kerja', 'lulusan_kuliah', 'lulusan_wirausaha',
                'guru_sertifikat', 'guru_non_linier'
            ])) {
                $data[$key] = 0;
            }
        }

        $monev->update($data);

        return redirect()->route('pengawas.monev-bulanan.index')->with('success', 'Laporan Monev Bulanan berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pengawasId = Auth::user()->id;
        
        $monev = MonevBulanan::where('id', $id)
            ->where('pengawas_id', $pengawasId)
            ->firstOrFail();
            
        $monev->delete();
        
        return redirect()->route('pengawas.monev-bulanan.index')->with('success', 'Laporan Monev Bulanan berhasil dihapus.');
    }
}
