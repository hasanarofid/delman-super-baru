@extends('layouts.pengawas.home')
@section('title', 'Buat Laporan Monev BOSP SMK')
@section('titelcard', 'Buat Laporan Monev BOSP SMK')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Formulir Monev BOSP SMK</h6>
                    </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger text-white">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pengawas.monev-bosp.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Sekolah Binaan -->
                            <div class="col-md-6 mb-3">
                                <label for="sekolah_id" class="form-label">Sekolah Binaan</label>
                                <select name="sekolah_id" id="sekolah_id" class="form-select select2" required>
                                    <option value="">Pilih Sekolah...</option>
                                    @foreach($sekolahBinaan as $sb)
                                        <option value="{{ $sb->sekolah->id }}" data-kota="{{ $sb->sekolah->kabupaten->nama_kabupaten ?? '' }}" data-status="{{ $sb->sekolah->status_sekolah ?? 'Swasta' }}">
                                            {{ $sb->sekolah->nama_sekolah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Kota/Kabupaten -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten</label>
                                <input type="text" id="kota" class="form-control" readonly>
                            </div>

                            <!-- Status Izin Operasional -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Izin Operasional</label>
                                <input type="text" id="status_ijop" class="form-control" readonly>
                            </div>

                            <!-- Bulan & Tahun -->
                            <div class="col-md-3 mb-3">
                                <label for="bulan" class="form-label">Bulan Pelaporan</label>
                                <select name="bulan" id="bulan" class="form-select" required>
                                    <option value="">Pilih Bulan...</option>
                                    @foreach($bulanOptions as $b)
                                        <option value="{{ $b }}" {{ old('bulan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="tahun" class="form-label">Tahun Pelaporan</label>
                                <input type="number" name="tahun" id="tahun" class="form-control" value="{{ $tahunSekarang }}" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">A. Data Siswa</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="siswa_kelas_10" class="form-label">Jumlah Siswa Kelas 10</label>
                                <input type="number" name="siswa_kelas_10" id="siswa_kelas_10" class="form-control hitung-siswa" required min="0" value="{{ old('siswa_kelas_10', 0) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="siswa_kelas_11" class="form-label">Jumlah Siswa Kelas 11</label>
                                <input type="number" name="siswa_kelas_11" id="siswa_kelas_11" class="form-control hitung-siswa" required min="0" value="{{ old('siswa_kelas_11', 0) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="siswa_kelas_12" class="form-label">Jumlah Siswa Kelas 12</label>
                                <input type="number" name="siswa_kelas_12" id="siswa_kelas_12" class="form-control hitung-siswa" required min="0" value="{{ old('siswa_kelas_12', 0) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Total Siswa Riil</label>
                                <input type="text" id="total_siswa_riil" class="form-control" readonly value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="siswa_dinas_bos" class="form-label">Data Siswa (Cutoff Dinas/BOS)</label>
                                <input type="number" name="siswa_dinas_bos" id="siswa_dinas_bos" class="form-control hitung-selisih" required min="0" value="{{ old('siswa_dinas_bos', 0) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status Kesesuaian Data</label>
                                <input type="text" id="status_kesesuaian" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3">B. Realisasi Keuangan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="realisasi_bosp_format" class="form-label">Total Realisasi BOSP (Rp)</label>
                                <input type="text" id="realisasi_bosp_format" class="form-control format-rupiah" required value="{{ old('realisasi_bosp', '') }}">
                                <input type="hidden" name="realisasi_bosp" id="realisasi_bosp" value="{{ old('realisasi_bosp', 0) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">C. Dokumen Pendukung & Catatan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="file_sptjm" class="form-label">Upload SPTJM (PDF/Image)</label>
                                <input type="file" name="file_sptjm" id="file_sptjm" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                <small class="text-muted d-block mt-1">Belum punya template? <a href="{{ asset('templates/template_sptjm_bosp.docx') }}" download="Template_SPTJM_BOSP.docx" target="_blank" class="text-primary font-weight-bold">Download Template SPTJM</a></small>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="catatan_observasi" class="form-label">Catatan Observasi</label>
                                <textarea name="catatan_observasi" id="catatan_observasi" rows="4" class="form-control">{{ old('catatan_observasi') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('pengawas.monev-bosp.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Laporan</button>
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

        // Autofill Data Sekolah
        $('#sekolah_id').change(function() {
            var selected = $(this).find('option:selected');
            var kota = selected.data('kota');
            var status = selected.data('status');
            
            $('#kota').val(kota);
            $('#status_ijop').val(status);
        });

        // Hitung Total Siswa Riil
        function hitungSiswa() {
            var k10 = parseInt($('#siswa_kelas_10').val()) || 0;
            var k11 = parseInt($('#siswa_kelas_11').val()) || 0;
            var k12 = parseInt($('#siswa_kelas_12').val()) || 0;
            
            var total = k10 + k11 + k12;
            $('#total_siswa_riil').val(total);
            
            hitungSelisih(total);
        }

        // Hitung Selisih
        function hitungSelisih(totalRiil) {
            var bos = parseInt($('#siswa_dinas_bos').val()) || 0;
            
            if (totalRiil === bos) {
                $('#status_kesesuaian').val('Sesuai');
                $('#status_kesesuaian').removeClass('bg-warning bg-danger text-white').addClass('bg-success text-white');
            } else if (totalRiil > bos) {
                $('#status_kesesuaian').val('Kelebihan ' + (totalRiil - bos) + ' Siswa');
                $('#status_kesesuaian').removeClass('bg-success bg-danger text-white').addClass('bg-warning text-dark');
            } else if (totalRiil < bos) {
                $('#status_kesesuaian').val('Kekurangan ' + (bos - totalRiil) + ' Siswa');
                $('#status_kesesuaian').removeClass('bg-success bg-warning text-dark').addClass('bg-danger text-white');
            }
        }

        $('.hitung-siswa').on('input', function() {
            hitungSiswa();
        });

        $('.hitung-selisih').on('input', function() {
            var total = parseInt($('#total_siswa_riil').val()) || 0;
            hitungSelisih(total);
        });
        
        // Trigger calculation on load if values exist (e.g. after validation fail)
        if ($('#sekolah_id').val()) {
            $('#sekolah_id').trigger('change');
            hitungSiswa();
        }

        // Format Rupiah
        $('.format-rupiah').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            if(val !== '') {
                $(this).val(parseInt(val).toLocaleString('id-ID'));
                $('#realisasi_bosp').val(val);
            } else {
                $(this).val('');
                $('#realisasi_bosp').val(0);
            }
        });
        
        // Trigger on load for old value
        if ($('#realisasi_bosp').val() && $('#realisasi_bosp').val() > 0) {
            var val = $('#realisasi_bosp').val();
            $('#realisasi_bosp_format').val(parseInt(val).toLocaleString('id-ID'));
        }
    });
</script>
@endsection
