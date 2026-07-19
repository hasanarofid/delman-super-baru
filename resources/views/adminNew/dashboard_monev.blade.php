@extends(Auth::user()->role == 'Pengawas' ? 'layouts.pengawas.home' : 'layouts.admin.home')
@section('title', 'Dashboard Monev')
@section('titelcard', 'Dashboard Monev')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Filter Data Monev</h6>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('admin.dashboard_monev') }}" method="GET" class="row gx-3 gy-2 align-items-center">
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
                        <h4 class="card-title text-white mb-2">{{ $metrics['total_laporan'] }}</h4>
                        <p class="card-text mb-0">Total Laporan Masuk</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-success h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ $metrics['total_mou'] }}</h4>
                        <p class="card-text mb-0">Total MoU Industri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-info h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ $metrics['total_prestasi'] }}</h4>
                        <p class="card-text mb-0">Jumlah Prestasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center text-white bg-warning h-100">
                    <div class="card-body">
                        <h4 class="card-title text-white mb-2">{{ number_format($metrics['avg_serapan_bosp'], 2) }}%</h4>
                        <p class="card-text mb-0">Rata-rata Serapan BOSP</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Dinamika Siswa Chart -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Dinamika Siswa (DO, Mutasi, Pindah)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="dinamikaSiswaChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Keterserapan Lulusan Chart -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Keterserapan Lulusan BKK</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="lulusanChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Jenis MoU Chart -->
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Distribusi Jenis MoU</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="mouChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Detail Laporan Monev Bulanan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pengawas</th>
                                <th>Sekolah</th>
                                <th>Bulan / Tahun</th>
                                <th>Total MoU</th>
                                <th>Serapan BOSP</th>
                                <th>Kondisi Bengkel</th>
                                <th>Total Lulusan (Bekerja)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monevData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->pengawas ? $data->pengawas->name : '-' }}</td>
                                <td>{{ $data->sekolah ? $data->sekolah->nama_sekolah : '-' }}</td>
                                <td>{{ $data->bulan }} / {{ $data->tahun }}</td>
                                <td>{{ $data->total_mou }}</td>
                                <td>{{ $data->serapan_bosp }}%</td>
                                <td>{{ $data->kondisi_bengkel ?? '-' }}</td>
                                <td>{{ $data->lulusan_kerja }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Data for Lulusan (Pie Chart)
        const lulusanData = @json(array_values($lulusanData));
        const lulusanLabels = @json(array_keys($lulusanData));
        
        const ctxLulusan = document.getElementById('lulusanChart').getContext('2d');
        new Chart(ctxLulusan, {
            type: 'pie',
            data: {
                labels: lulusanLabels,
                datasets: [{
                    data: lulusanData,
                    backgroundColor: ['#28c76f', '#00cfdd', '#ff9f43'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Data for Dinamika Siswa (Bar Chart)
        const dinamikaSiswaData = @json(array_values($dinamikaSiswaData));
        const dinamikaSiswaLabels = @json(array_keys($dinamikaSiswaData));

        const ctxDinamika = document.getElementById('dinamikaSiswaChart').getContext('2d');
        new Chart(ctxDinamika, {
            type: 'bar',
            data: {
                labels: dinamikaSiswaLabels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: dinamikaSiswaData,
                    backgroundColor: '#ea5455',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Data for MoU (Bar Chart)
        const mouData = @json(array_values($mouData));
        const mouLabels = @json(array_keys($mouData));

        const ctxMou = document.getElementById('mouChart').getContext('2d');
        new Chart(ctxMou, {
            type: 'bar',
            data: {
                labels: mouLabels,
                datasets: [{
                    label: 'Jumlah MoU',
                    data: mouData,
                    backgroundColor: '#7367f0',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        $('#data-table').DataTable();
    });
</script>
@endsection
