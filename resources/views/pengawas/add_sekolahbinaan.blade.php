@extends('layouts.admin.home')
@section('title', 'Set Sekolah Binaan')
@section('titelcard', 'Set Sekolah Binaan')

@section('style')
<style>
    .select2-selection__choice{
        background-color: #7367f0 !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Header Info Pengawas -->
        <div class="card mb-4 shadow-sm border-start border-primary border-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 text-primary fw-bold">
                        <i class="ti ti-user-check me-2"></i>{{ $models->name }}
                    </h5>
                    <p class="mb-0 text-muted small">
                        <strong>NIP:</strong> {{ $models->nip ?? '-' }} &nbsp;|&nbsp; 
                        <strong>Jabatan:</strong> {{ $models->jenjang_jabatan ?? 'Pengawas Sekolah' }} &nbsp;|&nbsp;
                        <strong>Wilayah:</strong> {{ $models->kabupaten->nama_kabupaten ?? '-' }}
                    </p>
                </div>
                <div>
                    <span class="badge bg-label-primary fs-6 px-3 py-2">
                        Total Binaan: <strong>{{ $total_binaan }} Sekolah</strong>
                    </span>
                    <a href="{{ route('masterpengawas.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            {{ Session::forget('success') }}
        @endif
                    
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Form Tambah Sekolah Binaan -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="ti ti-plus me-1"></i> Tambah / Kelola Pilihan Sekolah Binaan</h6>
                    </div>
                    <div class="card-body mt-3">
                        <form action="{{ route('masterpengawas.store_sekolah') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_pengawas" id="id_pengawas" value="{{ $models->id }}">
                     
                            <div class="form-group mb-3">
                                <label for="sekolah_id" class="form-label fw-semibold">Pilih Sekolah Binaan (Multi-Select)</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary" id="select-all">Select All</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="deselect-all">Deselect All</button>
                                </div>
                                <select name="sekolah_id[]" id="sekolah_id" class="form-select select2" multiple>
                                    @foreach ($sekolah as $item)
                                        <option value="{{ $item->id }}" {{ $binaan->contains('id_sekolah', $item->id) ? 'selected' : '' }}>
                                            {{ $item->nama_sekolah }} (NPSN: {{ $item->npsn ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Gunakan dropdown ini untuk menambah atau memperbarui sekolah binaan sekaligus.</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Pilihan Sekolah
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Sekolah Binaan Aktif (Dapat Dihapus Per Sekolah) -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="ti ti-building me-1 text-primary"></i> Daftar Sekolah Binaan Aktif ({{ $total_binaan }})
                        </h6>
                    </div>
                    <div class="card-body mt-3">
                        @if($binaan->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="ti ti-info-circle fs-2 d-block mb-2"></i>
                                Belum ada sekolah binaan yang terdaftar untuk pengawas ini.
                            </div>
                        @else
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Nama Sekolah</th>
                                            <th>NPSN</th>
                                            <th>Kabupaten / Kota</th>
                                            <th style="width: 120px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($binaan as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item->sekolah->nama_sekolah ?? '-' }}</strong>
                                                    @if(isset($item->sekolah->nama_sekolah))
                                                        @if(strpos(strtoupper($item->sekolah->nama_sekolah), 'SMK') !== false)
                                                            <span class="badge bg-label-info ms-1">SMK</span>
                                                        @elseif(strpos(strtoupper($item->sekolah->nama_sekolah), 'SMA') !== false)
                                                            <span class="badge bg-label-primary ms-1">SMA</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $item->sekolah->npsn ?? '-' }}</td>
                                                <td>{{ $item->sekolah->kabupaten->nama_kabupaten ?? ($item->sekolah->kota ?? '-') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('masterpengawas.deleteSekolahBinaan', $item->id) }}" 
                                                       class="btn btn-sm btn-danger px-2 py-1"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus sekolah {{ addslashes($item->sekolah->nama_sekolah ?? 'ini') }} dari binaan pengawas {{ addslashes($models->name) }}?')">
                                                        <i class="ti ti-trash me-1"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
    jQuery(document).ready(function () {
        var $select = jQuery('#sekolah_id').select2({
            placeholder: '.: Pilih Sekolah Binaan :.',
            allowClear: true
        });

        jQuery('#select-all').on('click', function() {
            var allOptions = [];
            jQuery('#sekolah_id option').each(function() {
                if (jQuery(this).val()) {
                    allOptions.push(jQuery(this).val());
                }
            });
            $select.val(allOptions).trigger('change');
        });

        jQuery('#deselect-all').on('click', function() {
            $select.val(null).trigger('change');
        });
    });
</script>
@endsection
