@extends('layouts.admin.home')
@section('title', 'Dashboard')
@section('titelcard', 'Dashboard')
@section('content')
@php
$jenjangOptions = ['SMA', 'SMK', 'SKh'];
if (isset($akses_jenjang) && !empty($akses_jenjang) && !in_array('All', $akses_jenjang)) {
    $jenjangOptions = $akses_jenjang;
}
@endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl-3 col-md-3 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Sekolah</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('sekolah.index') }}"
                                        class="btn btn-icon btn-success waves-effect waves-light">
                                        <span class="ti ti-school"></span>
                                    </a>
                                </div>
                            </div>
                            <h4 class="mb-0">{{ $total_sekolah }}</h4>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-trending-up me-1"></i>
                                    Data sekolah terdaftar
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Pengawas</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('masterpengawas.index') }}"
                                        class="btn btn-icon btn-info waves-effect waves-light">
                                        <span class="ti ti-user"></span>
                                    </a>
                                </div>
                            </div>
                            <h4 class="mb-0">{{ $total_pengawas }}</h4>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-users me-1"></i>
                                    Pengawas aktif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Rencana Kerja</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('rencanatugas.index') }}"
                                        class="btn btn-icon btn-danger waves-effect waves-light">
                                        <span class="ti ti-eye"></span>
                                    </a>
                                </div>
                            </div>
                            <h4 class="mb-0">{{ $total_rencankerja }}</h4>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-calendar me-1"></i>
                                    Rencana kerja aktif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Umpan Balik</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('listumpanbalik.index') }}"
                                        class="btn btn-icon btn-primary waves-effect waves-light">
                                        <span class="ti ti-briefcase"></span>
                                    </a>
                                </div>
                            </div>
                            <h4 class="mb-0">{{ $total_umpanbalik }}</h4>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-message-circle me-1"></i>
                                    Umpan balik diterima
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role == 'Stakeholder')
            @php
                $aksesKab = json_decode(Auth::user()->akses_kabupaten, true) ?? [];
                $aksesJen = json_decode(Auth::user()->akses_jenjang, true) ?? [];
                
                $kabNames = in_array('All', $aksesKab) || empty($aksesKab) ? ['Semua Kabupaten/Kota'] : $listKabupaten->pluck('nama_kabupaten')->toArray();
                $jenNames = in_array('All', $aksesJen) || empty($aksesJen) ? ['Semua Jenjang'] : $aksesJen;
            @endphp
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <span class="alert-icon text-primary me-2">
                            <i class="ti ti-info-circle ti-md"></i>
                        </span>
                        <div>
                            <strong>Informasi Hak Akses Anda:</strong><br>
                            Anda hanya dapat melihat data pada wilayah <b>{{ implode(', ', $kabNames) }}</b> 
                            untuk jenjang <b>{{ implode(', ', $jenNames) }}</b>.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h5 class="mb-0">Filter Global Dashboard</h5>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="global-filter-kabupaten" class="form-label">Pilih Kabupaten/Kota:</label>
                                            <select id="global-filter-kabupaten" class="select2 form-select">
                                                <option value="all">Semua Kabupaten/Kota</option>
                                                @foreach($listKabupaten as $kab)
                                                    <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="global-filter-jenjang" class="form-label">Pilih Jenjang:</label>
                                            <select id="global-filter-jenjang" class="select2 form-select">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">Semua Jenjang</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card chart-card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">
                                <i class="ti ti-chart-bar me-2"></i>
                                Grafik Jumlah Rencana per pengawas
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">


                                <div class="col-md-3">
                                    <label for="filter-kabupaten-1">Filter Kabupaten:</label>
                                    <select id="filter-kabupaten-1" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-bln">Filter Bulan:</label>
                                    <select id="filter-bln" name="bln" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($months as $month)
                                            <option value="{{ $month['name'] }}">
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select id="filter-tahun" name="tahun" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang">Filter Jenjang:</label>
                                    <select id="filter-jenjang" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <canvas id="pengawasChart"></canvas> <!-- Canvas for the chart -->

                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Umpan Balik per Rencana Kerja </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf2" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-2">

                                <div class="col-md-3">
                                    <label for="filter-kabupaten-2">Kabupaten:</label>
                                    <select id="filter-kabupaten-2" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-bln-last">Bulan:</label>
                                    <select id="filter-bln-last" name="bln" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($months as $month)
                                            <option value="{{ $month['name'] }}">
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun-last">Filter Tahun:</label>
                                    <select id="filter-tahun-last" name="tahun" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang-last">Filter Jenjang:</label>
                                    <select id="filter-jenjang-last" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">

                                    <label for="filter-pengawas">Filter by Pengawas:</label>
                                    <select id="filter-pengawas" name="pengawas" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($listPengawas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->nip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>
                            <canvas id="umpanbalikChart"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                {{-- begin spider web --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Jumlah Rencana Kerja per Raport Pendidikan </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf3" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-kabupaten-raport">Kabupaten:</label>
                                    <select id="filter-kabupaten-raport" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-bln-raport">Filter Bulan:</label>
                                    <select id="filter-bln-raport" name="bln" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($months as $month)
                                            <option value="{{ $month['name'] }}">
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun-raport">Filter Tahun:</label>
                                    <select id="filter-tahun-raport" name="tahun" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang-raport">Filter Jenjang:</label>
                                    <select id="filter-jenjang-raport" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <canvas id="chartPerRencanaKerja"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
                {{-- end spider web --}}

                {{-- begin spider web --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Jumlah Pengawasan Terkonfirmasi </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf4" class="btn btn-primary">Export PDF</button> <!-- Export button -->

                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label for="filter-kabupaten-konf">Kabupaten:</label>
                                    <select id="filter-kabupaten-konf" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-bln3">Filter Bulan:</label>
                                    <select id="filter-bln3" name="bln" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($months as $month)
                                            <option value="{{ $month['name'] }}">
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun3">Filter Tahun:</label>
                                    <select id="filter-tahun3" name="tahun" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang3">Filter Jenjang:</label>
                                    <select id="filter-jenjang3" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <canvas id="chartKonfrim"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                {{-- begin spider web --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Profil Kompetensi Pengawas </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf5" class="btn btn-primary btn-sm mb-3">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label for="filter-kabupaten-spider">Kabupaten:</label>
                                    <select id="filter-kabupaten-spider" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">

                                    <label for="filter-pengawas2">Filter by Pengawas:</label>
                                    <select id="filter-pengawas2" name="pengawas" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($listPengawas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->nip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun-spider"
                                        name="tahun"
                                        class="select2 form-select"
                                        required
                                    >
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang-spider">Filter Jenjang:</label>
                                    <select id="filter-jenjang-spider" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>
                            <canvas id="spiderWebPengawas"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
                {{-- end spider web --}}

                {{-- begin pie web --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0"> Realisasi Pelaksanaan Pengawasan </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf6" class="btn btn-primary btn-sm mb-3">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label for="filter-kabupaten-pie">Kabupaten:</label>
                                    <select id="filter-kabupaten-pie" class="select2 form-select filter-kabupaten">
                                        <option value="all">All</option>
                                        @foreach ($listKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">

                                    <label for="filter-pengawas3">Filter by Pengawas:</label>
                                    <select id="filter-pengawas3" name="pengawas" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($listPengawas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->nip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun-pie"
                                        name="tahun"
                                        class="select2 form-select"
                                        required
                                    >
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-jenjang-pie">Filter Jenjang:</label>
                                    <select id="filter-jenjang-pie" name="jenjang" class="select2 form-select filter-jenjang">
                                        @if(empty($akses_jenjang) || in_array('All', $akses_jenjang) || count($akses_jenjang) > 1)
                                            <option value="all">All</option>
                                        @endif
                                        @foreach($jenjangOptions as $j)
                                            <option value="{{ $j }}">{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>
                            <canvas id="piePengawas"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
                {{-- end pie web --}}
            </div>
            <div class="row mt-4">
                <div class="col-lg-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3 d-flex justify-content-between">
                            <h6 class="mb-0">Grafik Analisis Umpan Balik</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-center text-sm">Pengembangan Profesional</h6>
                                    <canvas id="chartQ1"></canvas>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-center text-sm">Aspek Kompetensi</h6>
                                    <canvas id="chartQ2"></canvas>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <h6 class="text-center text-sm">Kebermanfaatan</h6>
                                    <canvas id="chartQ4"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- jsPDF -->
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // diagram pie
            $('#filter-pengawas3').select2();
            $('#filter-tahun-pie').select2();
            $('#global-filter-kabupaten').select2();
            $('.filter-kabupaten').select2();

            let pieChartInstance = null;

            // Fungsi untuk mengambil dan menampilkan data chart pie
            function fetchChartDataPie(pengawas = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYearPie = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYearPie : year;
                fetch(`{{ route('admin.chartpie') }}?pengawas=${pengawas}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            console.warn('No data available for the chart');

                            // Hapus instance chart jika sudah ada
                            if (pieChartInstance) {
                                pieChartInstance.destroy();
                            }

                            // Tampilkan pesan "No data available" di canvas
                            const ctx = document.getElementById('piePengawas').getContext('2d');
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas
                                .height / 2);

                            return;
                        }

                        const pengawasNames = data.map(item => item.jawaban);
                        const rencanaCounts = data.map(item => item.total);

                        // Hapus instance chart jika sudah ada
                        if (pieChartInstance) {
                            pieChartInstance.destroy();
                        }

                        // Buat chart baru dengan type "pie"
                        const ctx = document.getElementById('piePengawas').getContext('2d');
                        pieChartInstance = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: pengawasNames,
                                datasets: [{
                                    label: 'Jumlah Umpan Balik',
                                    data: rencanaCounts,
                                    backgroundColor: [
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }
            document.getElementById('export-pdf6').addEventListener('click', function() {
                const canvas = document.getElementById('piePengawas');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-piePengawas.pdf');
            });

            // Load chart awal tanpa filter (semua data)
            const currentYearPie = new Date().getFullYear();
            fetchChartDataPie('all', currentYearPie, 'all', 'all');

            // Event listener untuk perubahan filter
            $('#filter-pengawas3, #filter-tahun-pie, #filter-kabupaten-pie, #filter-jenjang-pie').change(function() {
                const pengawas = $('#filter-pengawas3').val();
                let year = $('#filter-tahun-pie').val();
                const kabupaten = $('#filter-kabupaten-pie').val();
                const jenjang = $('#filter-jenjang-pie').val();
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartDataPie(pengawas, year, kabupaten, jenjang);
            });
            // end diagram pie
            //chart terkonfirmasi
            $('#filter-bln3').select2();
            $('#filter-tahun3').select2();
            $('#filter-kabupaten-konf').select2();

            let terkomfrimChartInstance = null;

            function fetchChartTerkonfrim(month = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYearKonf = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYearKonf : year;
                fetch(`{{ route('admin.chartTerkonfirmasi') }}?bln=${month}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        // Check if data is empty
                        if (!data || data.length === 0) {
                            console.warn('No data available for the chart');

                            // Destroy the existing chart instance if it exists
                            if (terkomfrimChartInstance) {
                                terkomfrimChartInstance.destroy();
                            }

                            // Display a "No data available" message in the canvas
                            const ctx = document.getElementById('chartKonfrim').getContext('2d');
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear previous content
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas
                                .height / 2);

                            return; // Exit early as there’s no data to display in the chart
                        }

                        const pengawasNames = data.map(item => item.pengawas);
                        const rencanaCounts = data.map(item => item.total);

                        // Destroy the existing chart instance if it exists
                        if (terkomfrimChartInstance) {
                            terkomfrimChartInstance.destroy();
                        }

                        // Set up the chart and assign it to chartPerRencanaKerjaInstance
                        const ctx = document.getElementById('chartKonfrim').getContext('2d');
                        terkomfrimChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: pengawasNames,
                                datasets: [{
                                    label: 'Jumlah Pengawasan',
                                    data: rencanaCounts,
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.2)'
                                        // , 'rgba(255, 159, 64, 0.2)', 'rgba(153, 102, 255, 0.2)',
                                        // 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(75, 192, 192, 1)'
                                        // , 'rgba(255, 159, 64, 1)', 'rgba(153, 102, 255, 1)',
                                        // 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Jumlah Pengawasan'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Pengawas'
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            document.getElementById('export-pdf4').addEventListener('click', function() {
                const canvas = document.getElementById('chartKonfrim');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-chartKonfrim.pdf');
            });

            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear3 = new Date().getFullYear();
            fetchChartTerkonfrim('all', currentYear3, 'all', 'all');

            // Event listener for filter changes
            $('#filter-bln3, #filter-tahun3, #filter-kabupaten-konf, #filter-jenjang3').change(function() {
                const month = $('#filter-bln3').val();
                let year = $('#filter-tahun3').val();
                const kabupaten = $('#filter-kabupaten-konf').val();
                const jenjang = $('#filter-jenjang3').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartTerkonfrim(month, year, kabupaten, jenjang);
            });


            // chart terkonfirmasi
            //chart per raport pendidikan
            $('#filter-bln-raport').select2();
            $('#filter-tahun-raport').select2();
            $('#filter-kabupaten-raport').select2();
            let raportPendidikanChartInstance = null;

            function fetchChartDataRaportPendidikan(month = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYearRaport = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYearRaport : year;
                fetch(`{{ route('admin.chartDataRaportPendidikan') }}?bln=${month}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        // Check if data is empty
                        if (!data || data.length === 0) {
                            console.warn('No data available for the chart');

                            // Destroy the existing chart instance if it exists
                            if (raportPendidikanChartInstance) {
                                raportPendidikanChartInstance.destroy();
                            }

                            // Display a "No data available" message in the canvas
                            const ctx = document.getElementById('chartPerRencanaKerja').getContext('2d');
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear previous content
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas
                                .height / 2);

                            return; // Exit early as there’s no data to display in the chart
                        }

                        const pengawasNames = data.map(item => item.aspekprogram);
                        const rencanaCounts = data.map(item => item.total);

                        // Destroy the existing chart instance if it exists
                        if (raportPendidikanChartInstance) {
                            raportPendidikanChartInstance.destroy();
                        }

                        // Set up the chart and assign it to chartPerRencanaKerjaInstance
                        const ctx = document.getElementById('chartPerRencanaKerja').getContext('2d');
                        raportPendidikanChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: pengawasNames,
                                datasets: [{
                                    label: 'Jumlah Rencana Kerja',
                                    data: rencanaCounts,
                                    backgroundColor: [
                                        // 'rgba(75, 192, 192, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                        //  'rgba(153, 102, 255, 0.2)',
                                        // 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 159, 64, 1)'

                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Jumlah Rencana Kerja'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Raport Pendidikan'
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            // export-pdf3
            document.getElementById('export-pdf3').addEventListener('click', function() {
                const canvas = document.getElementById('chartPerRencanaKerja');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-chartPerRencanaKerja.pdf');
            });


            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear4 = new Date().getFullYear();
            fetchChartDataRaportPendidikan('all', currentYear4, 'all', 'all');

            // Event listener for filter changes
            $('#filter-bln-raport, #filter-tahun-raport, #filter-kabupaten-raport, #filter-jenjang-raport').change(function() {
                const month = $('#filter-bln-raport').val();
                let year = $('#filter-tahun-raport').val();
                const kabupaten = $('#filter-kabupaten-raport').val();
                const jenjang = $('#filter-jenjang-raport').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartDataRaportPendidikan(month, year, kabupaten, jenjang);
            });

            // end chart per raport pendidikan

            $('#filter-bln').select2();
            $('#filter-tahun').select2();

            let pengawasChartInstance = null;



            function fetchChartData(month = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
                fetch(`{{ route('admin.chartData') }}?bln=${month}&tahun=${year}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        // Check if data is empty
                        if (!data || data.length === 0) {
                            console.warn('No data available for the chart');

                            // Destroy the existing chart instance if it exists
                            if (pengawasChartInstance) {
                                pengawasChartInstance.destroy();
                            }

                            // Display a "No data available" message in the canvas
                            const ctx = document.getElementById('pengawasChart').getContext('2d');
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear previous content
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas
                                .height / 2);

                            return; // Exit early as there’s no data to display in the chart
                        }

                        const pengawasNames = data.map(item => item.pengawas);
                        const rencanaCounts = data.map(item => item.total);

                        // Destroy the existing chart instance if it exists
                        if (pengawasChartInstance) {
                            pengawasChartInstance.destroy();
                        }

                        // Set up the chart and assign it to pengawasChartInstance
                        const ctx = document.getElementById('pengawasChart').getContext('2d');
                        pengawasChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: pengawasNames,
                                datasets: [{
                                    label: 'Jumlah Rencana Kerja',
                                    data: rencanaCounts,
                                    backgroundColor: [

                                        'rgba(153, 102, 255, 0.2)'
                                    ],
                                    borderColor: [

                                        'rgba(153, 102, 255, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Jumlah Rencana Kerja'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Pengawas'
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            // Export chart as PDF
            document.getElementById('export-pdf').addEventListener('click', function() {
                const canvas = document.getElementById('pengawasChart');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-export.pdf');
            });


            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear = new Date().getFullYear();
            fetchChartData('all', currentYear, 'all', 'all');

            // Event listener for filter changes
            $('#filter-bln, #filter-tahun, #filter-kabupaten-1, #filter-jenjang').change(function() {
                const month = $('#filter-bln').val();
                let year = $('#filter-tahun').val();
                const kabupaten = $('#filter-kabupaten-1').val();
                const jenjang = $('#filter-jenjang').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartData(month, year, kabupaten, jenjang);
            });

            $('#filter-bln-last').select2();
            $('#filter-tahun-last').select2();
            $('#filter-pengawas').select2();
            let umpanbalikChartInstance = null;

            function fetchChartData2(month = 'all', year = 'all', pengawas = 'all', kabupaten = 'all', jenjang = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYear = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYear : year;
                fetch(`{{ route('admin.chartData2') }}?bln=${month}&tahun=${filterYear}&pengawas=${pengawas}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            if (umpanbalikChartInstance) umpanbalikChartInstance.destroy();
                            const ctx = document.getElementById('umpanbalikChart').getContext('2d');
                            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                            ctx.font = '16px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas
                                .height / 2);
                            return;
                        }

                        const rencanaKerjaLabels = data.map(item => item.rencana_kerja);
                        const totalUmpanbalikData = data.map(item => item.total_umpan_balik);
                        const totalResponData = data.map(item => item.total_respon);

                        if (umpanbalikChartInstance) umpanbalikChartInstance.destroy();

                        const ctx = document.getElementById('umpanbalikChart').getContext('2d');
                        umpanbalikChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: rencanaKerjaLabels,
                                datasets: [{
                                        label: 'Respon Umpan Balik',
                                        data: totalResponData,
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Umpan Balik Terkirim',
                                        data: totalUmpanbalikData,
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                indexAxis: 'y',
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Jumlah'
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            // Export chart as PDF
            document.getElementById('export-pdf2').addEventListener('click', function() {
                const canvas = document.getElementById('umpanbalikChart');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-umpanbalikChart.pdf');
            });

            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear2 = new Date().getFullYear();
            fetchChartData2('all', currentYear2, 'all', 'all', 'all');

            // Event listener for filter changes
            $('#filter-bln-last, #filter-tahun-last, #filter-pengawas, #filter-kabupaten-2, #filter-jenjang-last').change(function() {

                const pengawas = $('#filter-pengawas').val();
                const month = $('#filter-bln-last').val();
                let year = $('#filter-tahun-last').val();
                const kabupaten = $('#filter-kabupaten-2').val();
                const jenjang = $('#filter-jenjang-last').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartData2(month, year, pengawas, kabupaten, jenjang);
            });

            $('#filter-pengawas2').select2();
            $('#filter-tahun-spider').select2();

            let spiderChartInstance;

            // Define category colors
            const categoryColors = {
                kemampuan_berinteraksi: 'rgba(54, 162, 235, 0.2)', // Light blue
                menciptakan_suasana: 'rgba(255, 99, 132, 0.2)', // Light red
                penguasaan_materi: 'rgba(75, 192, 192, 0.2)', // Light green
                kemampuan_komunikasi: 'rgba(153, 102, 255, 0.2)', // Light purple
                ketepatan_waktu: 'rgba(255, 159, 64, 0.2)' // Light orange
            };

            // Function to fetch chart data and display it
            function fetchSpiderWebData(pengawas = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYear = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYear : year;
                fetch(`{{ route('admin.spiderWebData') }}?pengawas=${pengawas}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
                    .then(response => response.json())
                    .then(data => {
                        if (spiderChartInstance) {
                            spiderChartInstance.destroy();
                        }

                        const ctx = document.getElementById('spiderWebPengawas').getContext('2d');

                        // Prepare dataset for the chart
                        const dataset = {
                            label: `Pengawas `,
                            data: [
                                data.kemampuan_berinteraksi,
                                data.menciptakan_suasana,
                                data.penguasaan_materi,
                                data.kemampuan_komunikasi,
                                data.ketepatan_waktu
                            ],
                            fill: true,
                            backgroundColor: [
                                categoryColors.kemampuan_berinteraksi,
                                categoryColors.menciptakan_suasana,
                                categoryColors.penguasaan_materi,
                                categoryColors.kemampuan_komunikasi,
                                categoryColors.ketepatan_waktu
                            ],
                            borderColor: [
                                'rgb(54, 162, 235)', // Blue
                                'rgb(255, 99, 132)', // Red
                                'rgb(75, 192, 192)', // Green
                                'rgb(153, 102, 255)', // Purple
                                'rgb(255, 159, 64)' // Orange
                            ],
                            pointBackgroundColor: [
                                'rgb(54, 162, 235)', // Blue
                                'rgb(255, 99, 132)', // Red
                                'rgb(75, 192, 192)', // Green
                                'rgb(153, 102, 255)', // Purple
                                'rgb(255, 159, 64)' // Orange
                            ],
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: [
                                'rgb(54, 162, 235)', // Blue
                                'rgb(255, 99, 132)', // Red
                                'rgb(75, 192, 192)', // Green
                                'rgb(153, 102, 255)', // Purple
                                'rgb(255, 159, 64)' // Orange
                            ]
                        };

                        // Configure the radar chart
                        spiderChartInstance = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels: ['Kemampuan berinteraksi', 'Menciptakan Suasana',
                                    'Penguasaan Materi', 'Kemampuan Komunikasi', 'Ketepatan Waktu'
                                ],
                                datasets: [dataset]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        max: 4, // Set max to match your rating scale (0-4 if 'Sangat Baik' is 4)
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching spider web data:', error));
            }

            // Export chart as PDF
            document.getElementById('export-pdf5').addEventListener('click', function() {
                const canvas = document.getElementById('spiderWebPengawas');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-spiderWebPengawas.pdf');
            });

            // Initial chart data load
            const currentYearSpider = new Date().getFullYear();
            fetchSpiderWebData('all', currentYearSpider);

            // Fetch data when the pengawas filter changes
            $('#filter-pengawas2, #filter-tahun-spider, #filter-kabupaten-spider, #filter-jenjang-spider').change(function() {
                const pengawas = $('#filter-pengawas2').val();
                let year = $('#filter-tahun-spider').val();
                const kabupaten = $('#filter-kabupaten-spider').val();
                const jenjang = $('#filter-jenjang-spider').val();
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchSpiderWebData(pengawas, year, kabupaten, jenjang);
            });


    // Dynamic Charts for Q1, Q2, Q4
    function fetchDynamicChart(questionId, canvasId, chartType, label, pengawas = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {
        const currentYear = new Date().getFullYear();
        const filterYear = (year === 'all') ? currentYear : year;

        fetch(`{{ route('admin.chartDynamicData') }}?question_id=${questionId}&pengawas=${pengawas}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`)
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById(canvasId).getContext('2d');
                const labels = data.map(item => item.answer);
                const counts = data.map(item => item.total);

                 const backgroundColors = [
                    'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'
                ];
                const borderColors = [
                    'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'
                ];

                const existingChart = Chart.getChart(canvasId);
                if (existingChart) existingChart.destroy();

                new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: counts,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: (chartType === 'radar') ? {
                            r: {
                                angleLines: {
                                    display: false
                                },
                                suggestedMin: 0
                            }
                        } : (chartType === 'pie') ? {} : {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching dynamic chart data:', error));
    }

    // Initial Load
    fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional');
    fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi');
    fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan');

    // Listener for shared filters
    $('#filter-pengawas3, #filter-tahun-pie, #filter-kabupaten-pie, #filter-jenjang-pie').change(function() {
         const pengawas = $('#filter-pengawas3').val();
         let year = $('#filter-tahun-pie').val();
         const kabupaten = $('#filter-kabupaten-pie').val();
         const jenjang = $('#filter-jenjang-pie').val();
        if (year === 'all') {
            year = new Date().getFullYear();
        }
        fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional', pengawas, year, kabupaten, jenjang);
        fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi', pengawas, year, kabupaten, jenjang);
        fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan', pengawas, year, kabupaten, jenjang);
    });

    // Global Filter Listener
    $('#global-filter-kabupaten').change(function() {
        const kabId = $(this).val();
        $('.filter-kabupaten').val(kabId).trigger('change');
    });

    $('#global-filter-jenjang').change(function() {
        const jenId = $(this).val();
        $('.filter-jenjang').val(jenId).trigger('change');
    });

        });

    </script>
