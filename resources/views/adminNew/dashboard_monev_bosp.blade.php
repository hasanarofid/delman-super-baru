@extends(Auth::user()->role == 'Pengawas' ? 'layouts.pengawas.home' : 'layouts.admin.home')
@section('title', 'Dashboard Monev Dapodik (BOSP)')
@section('titelcard', 'Dashboard Monev Dapodik (BOSP)')

@section('style')
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .btn, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate,
        .dataTables_wrapper .row:first-child, .dataTables_wrapper .row:last-child,
        footer, .content-footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .print-title {
            display: block !important;
            text-align: center;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 20px;
            color: #000;
        }
    }
    .print-title {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="print-title d-none d-print-block">
            Dashboard Data Monev Dapodik (BOSP) <br>
            Bulan {{ $month !== 'all' ? $month : 'Semua Bulan' }} Tahun {{ $year !== 'all' ? $year : 'Semua Tahun' }}
        </div>

        <!-- Filter Card -->
        <div class="card mb-4 d-print-none shadow-sm">
            <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary"><i class="ti ti-filter me-1"></i> Filter Data Monev Dapodik (BOSP)</h6>
                <div>
                    <a href="{{ route('admin.dashboard_monev_bosp.export', request()->query()) }}" class="btn btn-sm btn-success me-1"><i class="ti ti-file-spreadsheet me-1"></i> Download Excel</a>
                    <a href="{{ route('admin.dashboard_monev_bosp.pdf', request()->query()) }}" class="btn btn-sm btn-danger"><i class="ti ti-file-pdf me-1"></i> Download PDF</a>
                </div>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('admin.dashboard_monev_bosp') }}" method="GET" class="row gx-3 gy-2 align-items-center">
                    <div class="col-sm-3">
                        <label class="form-label mb-1 small fw-semibold" for="tahun">Tahun</label>
                        <select class="form-select" id="tahun" name="tahun">
                            <option value="all" {{ $year == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label mb-1 small fw-semibold" for="bulan">Bulan</label>
                        <select class="form-select" id="bulan" name="bulan">
                            <option value="all" {{ $month == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                            @foreach ($bulanOptions as $bln)
                                <option value="{{ $bln }}" {{ $month == $bln ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label mb-1 small fw-semibold" for="kabupaten_id">Kabupaten / Kota</label>
                        <select class="form-select" id="kabupaten_id" name="kabupaten_id">
                            <option value="all" {{ $selectedKabupaten == 'all' ? 'selected' : '' }}>Semua Kabupaten / Kota</option>
                            @foreach ($kabupatenOptions as $kab)
                                <option value="{{ $kab->id }}" {{ $selectedKabupaten == $kab->id ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto align-self-end">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
        <!-- Percentage & Progress Analytics Cards -->
        @if($isPengawas)
        <!-- Dashboard Progress untuk Pengawas -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-start border-primary border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title text-uppercase text-muted mb-0 fw-semibold">Capaian Monev Sekolah Dampingan</h6>
                            <span class="badge bg-primary fs-6">{{ $persentaseMonev }}%</span>
                        </div>
                        <h2 class="mb-1 text-primary fw-bold">{{ $sekolahSudahMonevCount }} <span class="fs-5 text-muted fw-normal">/ {{ $totalSekolahBinaan }} Sekolah</span></h2>
                        <p class="text-muted small mb-2">{{ $sekolahSudahMonevCount }} dari {{ $totalSekolahBinaan }} sekolah dampingan sudah di-monev</p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persentaseMonev }}%" aria-valuenow="{{ $persentaseMonev }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-start border-success border-4 shadow-sm text-center">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h1 class="text-success fw-bold mb-0">{{ $sekolahSudahMonevCount }}</h1>
                        <p class="card-text text-muted mb-0 font-weight-bold">Sekolah Sudah Monev</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-start border-danger border-4 shadow-sm text-center">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h1 class="text-danger fw-bold mb-0">{{ max(0, $totalSekolahBinaan - $sekolahSudahMonevCount) }}</h1>
                        <p class="card-text text-muted mb-0 font-weight-bold">Sekolah Belum Monev</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Dashboard Progress untuk Admin / Stakeholder (Kabid / Dinas) -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-start border-success border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title text-uppercase text-muted mb-0 fw-semibold">Keterlibatan Laporan Pengawas (Kabid View)</h6>
                            <span class="badge bg-success fs-6">{{ $persentasePengawasLapor }}%</span>
                        </div>
                        <h2 class="mb-1 text-success fw-bold">{{ $pengawasSudahLaporCount }} <span class="fs-5 text-muted fw-normal">/ {{ $totalPengawas }} Pengawas</span></h2>
                        <p class="text-muted small mb-2">{{ $pengawasSudahLaporCount }} dari {{ $totalPengawas }} Pengawas sudah membuat laporan monev</p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persentasePengawasLapor }}%" aria-valuenow="{{ $persentasePengawasLapor }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-start border-info border-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title text-uppercase text-muted mb-0 fw-semibold">Total Capaian Sekolah Dimonev (Wilayah)</h6>
                            <span class="badge bg-info fs-6">{{ $persentaseSekolahWilayah }}%</span>
                        </div>
                        <h2 class="mb-1 text-info fw-bold">{{ $totalSekolahDimonev }} <span class="fs-5 text-muted fw-normal">/ {{ $totalSekolahBinaanWilayah }} Total Sekolah Binaan</span></h2>
                        <p class="text-muted small mb-2">{{ $totalSekolahDimonev }} dari total {{ $totalSekolahBinaanWilayah }} sekolah binaan ter-monev di wilayah ini</p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persentaseSekolahWilayah }}%" aria-valuenow="{{ $persentaseSekolahWilayah }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekap Monitoring Keterlibatan Pengawas (Kabid & Admin View) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary font-weight-bold"><i class="ti ti-chart-bar me-1"></i> Monitoring Keterlibatan Pelaporan Pengawas (Kabid & Admin View)</h6>
                        <span class="badge bg-primary">{{ count($rekapKepatuhanPengawas) }} Pengawas Scope</span>
                    </div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Pengawas</th>
                                        <th>NIP / Identitas</th>
                                        <th class="text-center">Sekolah Binaan</th>
                                        <th class="text-center">Sudah Monev</th>
                                        <th style="width: 250px;">Progress Laporan</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapKepatuhanPengawas as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $item['nama'] }}</td>
                                        <td>{{ $item['nip'] }}</td>
                                        <td class="text-center fw-bold">{{ $item['total_binaan'] }} Sekolah</td>
                                        <td class="text-center fw-bold text-success">{{ $item['sudah_monev'] }} Sekolah</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold">{{ $item['persentase'] }}%</small>
                                                <small class="text-muted">{{ $item['sudah_monev'] }}/{{ $item['total_binaan'] }}</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $item['persentase'] >= 100 ? 'bg-success' : ($item['persentase'] > 0 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ $item['persentase'] }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $item['status_badge'] }} px-3 py-2 fs-7">{{ $item['status_text'] }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($isPengawas)
        <!-- Rekap Detail Sekolah Dampingan Pengawas (Pengawas View) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary font-weight-bold"><i class="ti ti-list-check me-1"></i> Status Pelaporan Sekolah Binaan Saya</h6>
                        <span class="badge bg-primary">{{ $totalSekolahBinaan }} Sekolah Dampingan</span>
                    </div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Sekolah Binaan</th>
                                        <th>NPSN</th>
                                        <th>Kabupaten</th>
                                        <th class="text-center">Status Monev (Bulan Ini)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sekolahDimonevIds = $monevList->pluck('sekolah_id')->unique()->toArray(); @endphp
                                    @foreach($sekolahBinaanList as $idx => $binaan)
                                    @php $sudah = in_array($binaan->id_sekolah, $sekolahDimonevIds); @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold text-dark">{{ $binaan->sekolah->nama_sekolah ?? '-' }}</td>
                                        <td>{{ $binaan->sekolah->npsn ?? '-' }}</td>
                                        <td>{{ $binaan->sekolah->kabupaten->nama_kabupaten ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($sudah)
                                                <span class="badge bg-success px-3 py-2"><i class="ti ti-check me-1"></i> Sudah Monev</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2"><i class="ti ti-x me-1"></i> Belum Monev</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Metrics Cards -->
        <div class="row mb-4">
            <div class="col-lg col-md-4 col-sm-6 mb-3">
                <div class="card text-center text-white bg-primary h-100">
                    <div class="card-body p-2">
                        <h4 class="card-title text-white mb-1">{{ $totalSekolahDimonev }}</h4>
                        <p class="card-text mb-0 small">Jumlah Sekolah yang sudah dimonev</p>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6 mb-3">
                <div class="card text-center text-white bg-info h-100">
                    <div class="card-body p-2">
                        <h4 class="card-title text-white mb-1">{{ number_format($totalSiswaRiil, 0, ',', '.') }}</h4>
                        <p class="card-text mb-0 small">Total siswa yang dimonev (Riil)</p>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6 mb-3">
                <div class="card text-center text-white bg-warning h-100">
                    <div class="card-body p-2">
                        <h4 class="card-title text-white mb-1">{{ $sekolahSelisihLebih }}</h4>
                        <p class="card-text mb-0 small">Total Selisih Kelebihan Siswa</p>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6 mb-3">
                <div class="card text-center text-white bg-danger h-100">
                    <div class="card-body p-2">
                        <h4 class="card-title text-white mb-1">{{ $sekolahSelisihKurang }}</h4>
                        <p class="card-text mb-0 small">Total Selisih Kekurangan Siswa</p>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6 mb-3">
                <div class="card text-center text-white bg-success h-100">
                    <div class="card-body p-2">
                        <h4 class="card-title text-white mb-1">Rp {{ number_format($totalRealisasiBosp, 0, ',', '.') }}</h4>
                        <p class="card-text mb-0 small">Total Realisasi BOSP</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="card h-100">
                    <div class="card-header pb-0 text-center">
                        <h6 class="mb-0">Proporsi Status Izin Operasional (IJOP)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ijopChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row">
            <!-- Table Lebih -->
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-warning">Data Aktual Siswa Berlebih</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sekolah</th>
                                        <th>Kabupaten</th>
                                        <th>Pengawas</th>
                                        <th>Selisih</th>
                                        <th class="d-print-none">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($monevList as $data)
                                        @if($data->total_siswa_riil > $data->siswa_dinas_bos)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $data->sekolah->nama_sekolah ?? '-' }}</td>
                                            <td>{{ $data->sekolah->kabupaten->nama_kabupaten ?? '-' }}</td>
                                            <td>{{ $data->pengawas->name ?? '-' }}</td>
                                            <td class="text-warning">+{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                            <td class="d-print-none">
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Sesuai -->
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-success">Data Siswa sesuai</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sekolah</th>
                                        <th>Kabupaten</th>
                                        <th>Pengawas</th>
                                        <th>Selisih</th>
                                        <th class="d-print-none">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($monevList as $data)
                                        @if($data->total_siswa_riil == $data->siswa_dinas_bos)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $data->sekolah->nama_sekolah ?? '-' }}</td>
                                            <td>{{ $data->sekolah->kabupaten->nama_kabupaten ?? '-' }}</td>
                                            <td>{{ $data->pengawas->name ?? '-' }}</td>
                                            <td class="text-success">0</td>
                                            <td class="d-print-none">
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Kurang -->
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-danger">Data Aktual Siswa Kurang</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable-custom">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sekolah</th>
                                        <th>Kabupaten</th>
                                        <th>Pengawas</th>
                                        <th>Selisih</th>
                                        <th class="d-print-none">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($monevList as $data)
                                        @if($data->total_siswa_riil < $data->siswa_dinas_bos)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $data->sekolah->nama_sekolah ?? '-' }}</td>
                                            <td>{{ $data->sekolah->kabupaten->nama_kabupaten ?? '-' }}</td>
                                            <td>{{ $data->pengawas->name ?? '-' }}</td>
                                            <td class="text-danger">{{ $data->total_siswa_riil - $data->siswa_dinas_bos }}</td>
                                            <td class="d-print-none">
                                                <button type="button" class="btn btn-sm btn-info btn-view-detail" 
                                                    data-info="{{ json_encode([
                                                        'sekolah' => $data->sekolah->nama_sekolah ?? '-',
                                                        'kabupaten' => $data->sekolah->kabupaten->nama_kabupaten ?? '-',
                                                        'pengawas' => $data->pengawas->name ?? '-',
                                                        'bulan' => $data->bulan,
                                                        'tahun' => $data->tahun,
                                                        'status_ijop' => $data->status_ijop,
                                                        'siswa_kelas_10' => $data->siswa_kelas_10,
                                                        'siswa_kelas_11' => $data->siswa_kelas_11,
                                                        'siswa_kelas_12' => $data->siswa_kelas_12,
                                                        'total_siswa_riil' => $data->total_siswa_riil,
                                                        'siswa_dinas_bos' => $data->siswa_dinas_bos,
                                                        'realisasi_bosp' => $data->realisasi_bosp,
                                                        'catatan' => $data->catatan_observasi,
                                                        'file' => $data->file_sptjm ? asset('public/sptjm/' . $data->file_sptjm) : ''
                                                    ]) }}">
                                                    View Detail
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalTitle">Detail Laporan Monev BOSP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Sekolah:</strong> <span id="det-sekolah"></span><br>
                <strong>Kabupaten:</strong> <span id="det-kabupaten"></span><br>
                <strong>Pengawas:</strong> <span id="det-pengawas"></span><br>
                <strong>Periode:</strong> <span id="det-periode"></span>
            </div>
            <div class="col-md-6">
                <strong>Status IJOP:</strong> <span id="det-ijop"></span><br>
                <div class="mt-2 p-2 rounded" id="det-status-container">
                    <strong>Status Data Siswa:</strong> <span id="det-status-siswa" class="fw-bold"></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mb-2">
            <div class="col-md-4">
                <strong>Siswa Kelas 10:</strong> <span id="det-k10"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 11:</strong> <span id="det-k11"></span>
            </div>
            <div class="col-md-4">
                <strong>Siswa Kelas 12:</strong> <span id="det-k12"></span>
            </div>
        </div>
        <div class="row mb-3 bg-light p-2 rounded align-items-center">
            <div class="col-md-6">
                <strong>Total Siswa Riil:</strong> <span id="det-riil" class="fs-5 fw-bold text-primary"></span>
            </div>
            <div class="col-md-6">
                <strong>Siswa Dinas BOS:</strong> <span id="det-bos" class="fs-5 fw-bold text-primary"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 bg-success text-white rounded">
                    <strong>Realisasi BOSP:</strong> <span class="fs-5 fw-bolder">Rp <span id="det-realisasi"></span></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 mb-2">
                <strong>Catatan Observasi:</strong>
                <p id="det-catatan" class="mt-1 mb-0 border p-2 bg-light rounded" style="min-height: 60px;"></p>
            </div>
            <div class="col-md-12 mt-2">
                <strong>File SPTJM:</strong>
                <div id="det-file-container" class="mt-1">
                    <a href="#" id="det-file" target="_blank" class="btn btn-sm btn-primary"><i class="ti ti-download me-1"></i> Download SPTJM</a>
                    <span id="det-no-file" class="text-muted" style="display:none;">Tidak ada file</span>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<!-- Memastikan library Chart.js ada (biasanya sudah ada dari layout induk, namun bisa ditambah jika belum) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    $(document).ready(function() {
        $('.datatable-custom').DataTable();

        // Register datalabels plugin
        Chart.register(ChartDataLabels);

        // Handle View Detail click
        $(document).on('click', '.btn-view-detail', function() {
            const data = $(this).data('info');
            
            $('#det-sekolah').text(data.sekolah);
            $('#det-kabupaten').text(data.kabupaten);
            $('#det-pengawas').text(data.pengawas);
            
            const monthNames = {
                '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
                '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
                '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
            };
            const monthName = monthNames[data.bulan] || data.bulan;
            $('#det-periode').text(monthName + ' ' + data.tahun);
            
            $('#det-ijop').text(data.status_ijop);
            $('#det-riil').text(data.total_siswa_riil);
            $('#det-bos').text(data.siswa_dinas_bos);
            
            // Set Status Data Siswa
            let statusText = '';
            let statusClass = '';
            if (parseInt(data.total_siswa_riil) > parseInt(data.siswa_dinas_bos)) {
                statusText = 'Data Aktual Siswa Berlebih';
                statusClass = 'bg-warning text-white border border-warning';
            } else if (parseInt(data.total_siswa_riil) < parseInt(data.siswa_dinas_bos)) {
                statusText = 'Data Aktual Siswa Kurang';
                statusClass = 'bg-danger text-white border border-danger';
            } else {
                statusText = 'Data Siswa Sesuai';
                statusClass = 'bg-success text-white border border-success';
            }
            $('#det-status-siswa').text(statusText);
            $('#det-status-container').removeClass().addClass('mt-2 p-2 rounded ' + statusClass);
            
            // Format currency
            const formatter = new Intl.NumberFormat('id-ID');
            $('#det-realisasi').text(formatter.format(data.realisasi_bosp));
            
            $('#det-k10').text(data.siswa_kelas_10);
            $('#det-k11').text(data.siswa_kelas_11);
            $('#det-k12').text(data.siswa_kelas_12);
            
            $('#det-catatan').text(data.catatan || '-');
            
            if (data.file) {
                $('#det-file').attr('href', data.file).show();
                $('#det-no-file').hide();
            } else {
                $('#det-file').hide();
                $('#det-no-file').show();
            }
            
            $('#detailModal').modal('show');
        });

        // Data for Status IJOP (Pie Chart)
        const ijopData = @json(array_values($statusIjopData));
        const ijopLabels = @json(array_keys($statusIjopData));

        const ctxIjop = document.getElementById('ijopChart').getContext('2d');
        new Chart(ctxIjop, {
            type: 'pie',
            data: {
                labels: ijopLabels,
                datasets: [{
                    label: 'Jumlah Sekolah',
                    data: ijopData,
                    backgroundColor: [
                        '#7367f0', // Primary
                        '#28c76f', // Success
                        '#ff9f43', // Warning
                        '#ea5455', // Danger
                        '#00cfe8', // Info
                        '#82868b'  // Secondary
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function(value, context) {
                            return value; // Display the raw count
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
