@extends('layouts.admin.home')
@section('title', 'Pengaturan Buka/Tutup Aspek Pendidikan')
@section('titelcard', 'Pengaturan Buka/Tutup Aspek Pendidikan Periodik')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="mb-0 font-weight-bold">
                                        <i class="ti ti-toggle-right text-primary me-2"></i>Pengaturan Akses Aspek Raport Pendidikan Periodik
                                    </h5>
                                    <small class="text-muted">Tentukan Aspek Raport Pendidikan yang DIBUKA / DITUTUP untuk pengawas pada periode dan wilayah tertentu.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-3">
                        @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('aspekprogram.pengaturan') }}" class="mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" for="filter-kabupaten">Kabupaten / Wilayah:</label>
                                    <select id="filter-kabupaten" name="kabupaten_id" class="select2 form-select" onchange="this.form.submit()">
                                        <option value="">-- Semua Wilayah (Global) --</option>
                                        @foreach($kabupatens as $kab)
                                            <option value="{{ $kab->id }}" {{ $selectedKabupatenId == $kab->id ? 'selected' : '' }}>
                                                {{ $kab->nama_kabupaten }} ({{ $kab->kelompok_kabupaten }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" for="filter-jenjang">Jenjang Sekolah:</label>
                                    <select id="filter-jenjang" name="jenjang" class="select2 form-select" onchange="this.form.submit()">
                                        <option value="">-- Semua Jenjang --</option>
                                        @foreach($listJenjang as $jnj)
                                            <option value="{{ $jnj }}" {{ $selectedJenjang == $jnj ? 'selected' : '' }}>
                                                Jenjang {{ $jnj }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-bold" for="filter-bulan">Bulan:</label>
                                    <select id="filter-bulan" name="bulan" class="select2 form-select" onchange="this.form.submit()">
                                        @foreach($months as $val => $name)
                                            <option value="{{ $val }}" {{ $selectedBulan == $val ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-bold" for="filter-tahun">Tahun:</label>
                                    <select id="filter-tahun" name="tahun" class="select2 form-select" onchange="this.form.submit()">
                                        @foreach($years as $yr)
                                            <option value="{{ $yr }}" {{ $selectedTahun == $yr ? 'selected' : '' }}>
                                                {{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light w-100">
                                        <i class="ti ti-filter me-1"></i> Filter Data
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">

                        <!-- Form Save Settings -->
                        <form method="POST" action="{{ route('aspekprogram.storePengaturan') }}">
                            @csrf
                            <input type="hidden" name="kabupaten_id" value="{{ $selectedKabupatenId }}">
                            <input type="hidden" name="jenjang" value="{{ $selectedJenjang }}">
                            <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                            <input type="hidden" name="tahun" value="{{ $selectedTahun }}">

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" for="target-pengawas">Target Pengawas (Opsional):</label>
                                    <select id="target-pengawas" name="pengawas_id" class="select2 form-select">
                                        <option value="">-- Berlaku untuk Semua Pengawas di Wilayah ini --</option>
                                        @foreach($pengawases as $pengawas)
                                            <option value="{{ $pengawas->id }}">{{ $pengawas->name }} (NIP: {{ $pengawas->nip ?: '-' }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">Pilih pengawas spesifik jika ingin memberikan aturan khusus per pengawas.</small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-sm font-weight mb-1">NO</th>
                                            <th class="text-sm font-weight mb-1">NAMA ASPEK RAPORT PENDIDIKAN</th>
                                            <th width="30%" class="text-center text-sm font-weight mb-1">STATUS AKSES UNTUK PENGAWAS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($aspekPrograms as $index => $aspek)
                                            @php
                                                $keyGlobal = $aspek->id . '_global';
                                                $existing = isset($pengaturanExisting[$keyGlobal]) ? $pengaturanExisting[$keyGlobal] : null;
                                                $isActive = $existing ? $existing->is_active : 1;
                                            @endphp
                                            <tr>
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <span class="fw-semibold">{{ $aspek->nama }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input type="hidden" name="aspek_status[{{ $aspek->id }}]" value="0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                               id="aspek_switch_{{ $aspek->id }}"
                                                               name="aspek_status[{{ $aspek->id }}]" value="1"
                                                               {{ $isActive ? 'checked' : '' }}>
                                                        <label class="form-check-label ms-2" for="aspek_switch_{{ $aspek->id }}">
                                                            @if($isActive)
                                                                <span class="badge bg-label-success">DIBUKA (Dapat Dikerjakan)</span>
                                                            @else
                                                                <span class="badge bg-label-danger">DITUTUP (Terkunci)</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada data Aspek Raport Pendidikan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light btn-lg">
                                    <i class="ti ti-device-floppy me-1"></i> Simpan Pengaturan Aspek
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%'
            });
        }
    });
</script>
@endsection
