@extends('layouts.pengawas.home')
@section('title', 'Edit Laporan Monev Bulanan')
@section('titelcard', 'Edit Laporan Monev Bulanan')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Form Input Laporan Monev Bulanan SMK</h6>
                    </div>
                    <div class="card-body">
                        @if (Session::has('error'))
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('pengawas.monev-bulanan.update', $monev->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <h5 class="mt-3 border-bottom pb-2">Informasi Laporan</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sekolah Binaan <span class="text-danger">*</span></label>
                                    <select name="sekolah_id" class="form-select select2" required>
                                        <option value="">-- Pilih Sekolah --</option>
                                        @foreach($sekolahBinaan as $binaan)
                                            @if($binaan->sekolah)
                                                <option value="{{ $binaan->sekolah->id }}" {{ old('sekolah_id', $monev->sekolah_id) == $binaan->sekolah->id ? 'selected' : '' }}>
                                                    {{ $binaan->sekolah->nama_sekolah }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                    <select name="bulan" class="form-select select2" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        @foreach($bulanOptions as $bulan)
                                            <option value="{{ $bulan }}" {{ old('bulan', $monev->bulan) == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $monev->tahun) }}" required>
                                </div>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Data Umum Sekolah</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Akreditasi</label>
                                    <select name="akreditasi" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="A" {{ old('akreditasi') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('akreditasi') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('akreditasi') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="Belum Akreditasi" {{ old('akreditasi') == 'Belum Akreditasi' ? 'selected' : '' }}>Belum Akreditasi</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Kurikulum Implementasi</label>
                                    <select name="kurikulum" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="K13" {{ old('kurikulum') == 'K13' ? 'selected' : '' }}>K13</option>
                                        <option value="Kurikulum Merdeka" {{ old('kurikulum') == 'Kurikulum Merdeka' ? 'selected' : '' }}>Kurikulum Merdeka</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Memiliki BKK?</label>
                                    <select name="bkk" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="Ya" {{ old('bkk') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                        <option value="Tidak" {{ old('bkk') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Kondisi Bengkel/Lab</label>
                                    <select name="kondisi_bengkel" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="Sangat Baik" {{ old('kondisi_bengkel') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="Baik" {{ old('kondisi_bengkel') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Cukup" {{ old('kondisi_bengkel') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Kurang" {{ old('kondisi_bengkel') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total MoU Industri</label>
                                    <input type="number" name="total_mou" class="form-control" value="{{ old('total_mou', $monev->total_mou) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jumlah Prestasi</label>
                                    <input type="number" name="jumlah_prestasi" class="form-control" value="{{ old('jumlah_prestasi', $monev->jumlah_prestasi) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Keterserapan BOS/BOP (%)</label>
                                    <input type="number" step="0.01" name="serapan_bosp" class="form-control" value="{{ old('serapan_bosp', $monev->serapan_bosp) }}">
                                </div>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Dinamika Siswa</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Siswa Drop Out (DO)</label>
                                    <input type="number" name="siswa_do" class="form-control" value="{{ old('siswa_do', $monev->siswa_do) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Siswa Mutasi Keluar</label>
                                    <input type="number" name="siswa_mutasi" class="form-control" value="{{ old('siswa_mutasi', $monev->siswa_mutasi) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Siswa Pindahan</label>
                                    <input type="number" name="siswa_pindahan" class="form-control" value="{{ old('siswa_pindahan', $monev->siswa_pindahan) }}">
                                </div>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Kerusakan Sarana Prasarana</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Ruangan</th>
                                            <th>Baik</th>
                                            <th>Rusak Ringan (RR)</th>
                                            <th>Rusak Sedang (RS)</th>
                                            <th>Rusak Berat (RB)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Ruang Kelas</td>
                                            <td><input type="number" name="sarpras_kelas_baik" class="form-control form-control-sm" value="{{ old('sarpras_kelas_baik', $monev->sarpras_kelas_baik) }}"></td>
                                            <td><input type="number" name="sarpras_kelas_rr" class="form-control form-control-sm" value="{{ old('sarpras_kelas_rr', $monev->sarpras_kelas_rr) }}"></td>
                                            <td><input type="number" name="sarpras_kelas_rs" class="form-control form-control-sm" value="{{ old('sarpras_kelas_rs', $monev->sarpras_kelas_rs) }}"></td>
                                            <td><input type="number" name="sarpras_kelas_rb" class="form-control form-control-sm" value="{{ old('sarpras_kelas_rb', $monev->sarpras_kelas_rb) }}"></td>
                                        </tr>
                                        <tr>
                                            <td>RPS (Ruang Praktik Siswa)</td>
                                            <td><input type="number" name="sarpras_rps_baik" class="form-control form-control-sm" value="{{ old('sarpras_rps_baik', $monev->sarpras_rps_baik) }}"></td>
                                            <td><input type="number" name="sarpras_rps_rr" class="form-control form-control-sm" value="{{ old('sarpras_rps_rr', $monev->sarpras_rps_rr) }}"></td>
                                            <td><input type="number" name="sarpras_rps_rs" class="form-control form-control-sm" value="{{ old('sarpras_rps_rs', $monev->sarpras_rps_rs) }}"></td>
                                            <td><input type="number" name="sarpras_rps_rb" class="form-control form-control-sm" value="{{ old('sarpras_rps_rb', $monev->sarpras_rps_rb) }}"></td>
                                        </tr>
                                        <tr>
                                            <td>Laboratorium</td>
                                            <td><input type="number" name="sarpras_lab_baik" class="form-control form-control-sm" value="{{ old('sarpras_lab_baik', $monev->sarpras_lab_baik) }}"></td>
                                            <td><input type="number" name="sarpras_lab_rr" class="form-control form-control-sm" value="{{ old('sarpras_lab_rr', $monev->sarpras_lab_rr) }}"></td>
                                            <td><input type="number" name="sarpras_lab_rs" class="form-control form-control-sm" value="{{ old('sarpras_lab_rs', $monev->sarpras_lab_rs) }}"></td>
                                            <td><input type="number" name="sarpras_lab_rb" class="form-control form-control-sm" value="{{ old('sarpras_lab_rb', $monev->sarpras_lab_rb) }}"></td>
                                        </tr>
                                        <tr>
                                            <td>Perpustakaan</td>
                                            <td><input type="number" name="sarpras_perpus_baik" class="form-control form-control-sm" value="{{ old('sarpras_perpus_baik', $monev->sarpras_perpus_baik) }}"></td>
                                            <td><input type="number" name="sarpras_perpus_rr" class="form-control form-control-sm" value="{{ old('sarpras_perpus_rr', $monev->sarpras_perpus_rr) }}"></td>
                                            <td><input type="number" name="sarpras_perpus_rs" class="form-control form-control-sm" value="{{ old('sarpras_perpus_rs', $monev->sarpras_perpus_rs) }}"></td>
                                            <td><input type="number" name="sarpras_perpus_rb" class="form-control form-control-sm" value="{{ old('sarpras_perpus_rb', $monev->sarpras_perpus_rb) }}"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Jenis MoU Industri</h5>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Penyelarasan kurikulum</label>
                                    <input type="number" name="mou_kurikulum" class="form-control" value="{{ old('mou_kurikulum', $monev->mou_kurikulum) }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Peningkatan kompetensi guru</label>
                                    <input type="number" name="mou_guru" class="form-control" value="{{ old('mou_guru', $monev->mou_guru) }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Peningkatan kompetensi siswa (pkl)</label>
                                    <input type="number" name="mou_murid" class="form-control" value="{{ old('mou_murid', $monev->mou_murid) }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Sertifikasi (LSP atau Ujikom)</label>
                                    <input type="number" name="mou_sertifikasi" class="form-control" value="{{ old('mou_sertifikasi', $monev->mou_sertifikasi) }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Rekruitmen</label>
                                    <input type="number" name="mou_rekrutmen" class="form-control" value="{{ old('mou_rekrutmen', $monev->mou_rekrutmen) }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">MoU Bantuan atau CSR</label>
                                    <input type="number" name="mou_csr" class="form-control" value="{{ old('mou_csr', $monev->mou_csr) }}">
                                </div>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Keterserapan Lulusan (BKK)</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bekerja</label>
                                    <input type="number" name="lulusan_kerja" class="form-control" value="{{ old('lulusan_kerja', $monev->lulusan_kerja) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Melanjutkan Kuliah</label>
                                    <input type="number" name="lulusan_kuliah" class="form-control" value="{{ old('lulusan_kuliah', $monev->lulusan_kuliah) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Wirausaha</label>
                                    <input type="number" name="lulusan_wirausaha" class="form-control" value="{{ old('lulusan_wirausaha', $monev->lulusan_wirausaha) }}">
                                </div>
                            </div>

                            <h5 class="mt-4 border-bottom pb-2">Profil Kompetensi Guru</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guru Bersertifikat Pendidik (%)</label>
                                    <input type="number" step="0.01" name="guru_sertifikat" class="form-control" value="{{ old('guru_sertifikat', $monev->guru_sertifikat) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guru Tidak Linier (%)</label>
                                    <input type="number" step="0.01" name="guru_non_linier" class="form-control" value="{{ old('guru_non_linier', $monev->guru_non_linier) }}">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update Laporan</button>
                                <a href="{{ route('pengawas.monev-bulanan.index') }}" class="btn btn-secondary">Batal</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
