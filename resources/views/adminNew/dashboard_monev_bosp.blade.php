@extends(Auth::user()->role == 'Pengawas' ? 'layouts.pengawas.home' : 'layouts.admin.home')
@section('title', 'Dashboard Monev BOSP SMK')
@section('titelcard', 'Dashboard Monev BOSP SMK')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Filter Data Monev BOSP SMK</h6>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('admin.dashboard_monev_bosp') }}" method="GET" class="row gx-3 gy-2 align-items-center">
                    <div class="col-sm-3">
                        <label class="visually-hidden" for="tahun">Tahun</label>
                        <select class="form-select" id="tahun" name="tahun">
                            <option value="all" {{ $year == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="visually-hidden" for="bulan">Bulan</label>
                        <select class="form-select" id="bulan" name="bulan">
                            <option value="all" {{ $month == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                            @foreach ($bulanOptions as $bln)
                                <option value="{{ $bln }}" {{ $month == $bln ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-primary h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ $totalSekolahDimonev }}</h4>
                        <p class="card-text mb-0">Jumlah Sekolah yang sudah dimonev</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-info h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ number_format($totalSiswaRiil, 0, ',', '.') }}</h4>
                        <p class="card-text mb-0">Total siswa yang dimonev (Riil)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-warning h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ $sekolahSelisihLebih }}</h4>
                        <p class="card-text mb-0">Total Selisih Kelebihan Siswa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-danger h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ $sekolahSelisihKurang }}</h4>
                        <p class="card-text mb-0">Total Selisih Kekurangan Siswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row">
            <!-- Table Lebih -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-warning">Daftar Sekolah Data Lebih (Aktual > BOS)</h6>
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
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0 text-danger">Daftar Sekolah Data Kurang (Aktual < BOS)</h6>
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.datatable-custom').DataTable();
    });
</script>
@endsection
