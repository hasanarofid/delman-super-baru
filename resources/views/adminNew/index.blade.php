@extends('layouts.admin.home')
@section('title', 'Dashboard')
@section('titelcard', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl-3 col-md-4 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Sekolah</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('sekolah.index') }}" class="btn btn-icon btn-success waves-effect waves-light">
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
                <div class="col-xl-3 col-md-4 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Pengawas</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('masterpengawas.index') }}" class="btn btn-icon btn-info waves-effect waves-light">
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
                <div class="col-xl-3 col-md-4 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Rencana Kerja</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('rencanatugas.index') }}"  class="btn btn-icon btn-danger waves-effect waves-light">
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
                <div class="col-xl-3 col-md-4 col-6 mb-4">
                    <div class="card h-100 dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-1 pt-2">Total Umpan Balik</h5>
                                <div class="badge p-2 rounded">
                                    <a href="{{ route('listumpanbalik.index') }}" class="btn btn-icon btn-primary waves-effect waves-light">
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

            <div class="row mt-4">

                <div class="col-lg-12 mb-3">
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


                                <div class="col-md-6">
                                    <label for="filter-pengawas">Filter Bulan:</label>
                                    <select
                                    id="filter-bln"
                                    name="bln"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach($months as $month)
                                        <option value="{{ $month['name'] }}">
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>

                                <div class="col-md-6">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun"
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

                            </div>
                            <canvas id="pengawasChart"></canvas> <!-- Canvas for the chart -->

                        </div>
                  </div>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Grafik Umpan Balik per Rencana Kerja </h6>
                      </div>
                          <div class="card-body p-3">
                            <button id="export-pdf2" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-2">

                                <div class="col-md-3">
                                    <label for="filter-pengawas">Bulan:</label>
                                    <select
                                    id="filter-bln-last"
                                    name="bln"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach($months as $month)
                                        <option value="{{ $month['name'] }}">
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>

                                <div class="col-md-3">
                                    <label for="filter-tahun-last">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun-last"
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

                                <div class="col-md-6">

                                    <label for="filter-pengawas">Filter by Pengawas:</label>
                                    <select
                                    id="filter-pengawas"
                                    name="pengawas"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach ($listPengawas as $item)
                                        <option value="{{ $item->id }}">{{ $item->name.' - '.$item->nip }}</option>
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
                <div class="col-12 mb-3">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Grafik Jumlah Rencana Kerja per Raport Pendidikan </h6>
                      </div>
                          <div class="card-body p-3">
                            <button id="export-pdf3" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">


                                <div class="col-md-6">
                                    <label for="filter-pengawas">Filter Bulan:</label>
                                    <select
                                    id="filter-bln2"
                                    name="bln"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach($months as $month)
                                        <option value="{{ $month['name'] }}">
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>

                                <div class="col-md-6">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun2"
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

                            </div>
                            <canvas id="chartPerRencanaKerja"></canvas> <!-- Canvas for the chart -->
                          </div>
                    </div>
                </div>
                {{-- end spider web --}}

                 {{-- begin spider web --}}
                 <div class="col-lg-12 mb-3">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Grafik Jumlah Pendampingan Terkonfirmasi </h6>
                      </div>
                          <div class="card-body p-3">
                            <button id="export-pdf4" class="btn btn-primary">Export PDF</button> <!-- Export button -->

                            <div class="row mb-3">


                                <div class="col-md-6">
                                    <label for="filter-pengawas">Filter Bulan:</label>
                                    <select
                                    id="filter-bln3"
                                    name="bln"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach($months as $month)
                                        <option value="{{ $month['name'] }}">
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>

                                <div class="col-md-6">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select
                                        id="filter-tahun3"
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

                            </div>
                            <canvas id="chartKonfrim"></canvas> <!-- Canvas for the chart -->
                          </div>
                    </div>
                </div>


            </div>


            <div class="row mt-4">
                {{-- begin spider web --}}
                <div class="col-lg-12 mb-3">
                   <div class="card">
                     <div class="card-header pb-0 p-3">
                       <h6 class="mb-0">Profil Kompetensi Pengawas </h6>
                     </div>
                         <div class="card-body p-3">
                            <button id="export-pdf5" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                           <div class="row mb-3">


                               <div class="col-md-12">

                                   <label for="filter-pengawas">Filter by Pengawas:</label>
                                   <select
                                   id="filter-pengawas2"
                                   name="pengawas"
                                   class="select2 form-select"
                                   required
                               >
                                   <option value="all">All</option> <!-- Option to show all records -->
                                   @foreach ($listPengawas as $item)
                                       <option value="{{ $item->id }}">{{ $item->name.' - '.$item->nip }}</option>
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
                <div class="col-lg-12 mb-3">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0"> Realisasi Pelaksanaan Pendampingan </h6>
                      </div>
                          <div class="card-body p-3">
                            <button id="export-pdf6" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">

                                <div class="col-md-12">

                                    <label for="filter-pengawas">Filter by Pengawas:</label>
                                    <select
                                    id="filter-pengawas3"
                                    name="pengawas"
                                    class="select2 form-select"
                                    required
                                >
                                    <option value="all">All</option> <!-- Option to show all records -->
                                    @foreach ($listPengawas as $item)
                                        <option value="{{ $item->id }}">{{ $item->name.' - '.$item->nip }}</option>
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

let pieChartInstance = null;

// Fungsi untuk mengambil dan menampilkan data chart pie
function fetchChartDataPie(pengawas = 'all') {
    fetch(`{{ route('admin.chartpie') }}?pengawas=${pengawas}`)
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
                ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas.height / 2);

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
document.getElementById('export-pdf6').addEventListener('click', function () {
            const canvas = document.getElementById('piePengawas');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-piePengawas.pdf');
        });

// Load chart awal tanpa filter (semua data)
fetchChartDataPie();

// Event listener untuk perubahan filter
$('#filter-pengawas3').change(function() {
    const pengawas = $('#filter-pengawas3').val();
    fetchChartDataPie(pengawas);
});
    // end diagram pie
    //chart terkonfirmasi
$('#filter-bln3').select2();
$('#filter-tahun3').select2();
    
    let terkomfrimChartInstance = null;
function fetchChartTerkonfrim(month = 'all', year = 'all') {
    fetch(`{{ route('admin.chartTerkonfirmasi') }}?bln=${month}&tahun=${year}`)
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
                ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas.height / 2);

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
                        label: 'Jumlah Pendampingan',
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
                            title: { display: true, text: 'Jumlah Pendampingan' }
                        },
                        x: {
                            title: { display: true, text: 'Pengawas' }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

document.getElementById('export-pdf4').addEventListener('click', function () {
            const canvas = document.getElementById('chartKonfrim');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-chartKonfrim.pdf');
        });

fetchChartTerkonfrim();

// Event listener for filter changes
$('#filter-bln3, #filter-tahun3').change(function() {
    const month = $('#filter-bln3').val();
    const year = $('#filter-tahun3').val();
    fetchChartTerkonfrim(month, year);
});


// chart terkonfirmasi
//chart per raport pendidikan
$('#filter-bln2').select2();
$('#filter-tahun2').select2();
let raportPendidikanChartInstance = null;
function fetchChartDataRaportPendidikan(month = 'all', year = 'all') {
    fetch(`{{ route('admin.chartDataRaportPendidikan') }}?bln=${month}&tahun=${year}`)
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
                ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas.height / 2);

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
                            title: { display: true, text: 'Jumlah Rencana Kerja' }
                        },
                        x: {
                            title: { display: true, text: 'Raport Pendidikan' }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

// export-pdf3
document.getElementById('export-pdf3').addEventListener('click', function () {
            const canvas = document.getElementById('chartPerRencanaKerja');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-chartPerRencanaKerja.pdf');
        });


fetchChartDataRaportPendidikan();

// Event listener for filter changes
$('#filter-bln2, #filter-tahun2').change(function() {
    const month = $('#filter-bln2').val();
    const year = $('#filter-tahun2').val();
    fetchChartDataRaportPendidikan(month, year);
});

// end chart per raport pendidikan

        $('#filter-bln').select2();
        $('#filter-tahun').select2();

        let pengawasChartInstance = null;

function fetchChartData(month = 'all', year = 'all') {
    fetch(`{{ route('admin.chartData') }}?bln=${month}&tahun=${year}`)
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
                ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas.height / 2);

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
                            title: { display: true, text: 'Jumlah Rencana Kerja' }
                        },
                        x: {
                            title: { display: true, text: 'Pengawas' }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

  // Export chart as PDF
  document.getElementById('export-pdf').addEventListener('click', function () {
            const canvas = document.getElementById('pengawasChart');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-export.pdf');
        });


// Initial chart load with no filters (all data)
fetchChartData();

// Event listener for filter changes
$('#filter-bln, #filter-tahun').change(function() {
    const month = $('#filter-bln').val();
    const year = $('#filter-tahun').val();
    fetchChartData(month, year);
});

$('#filter-bln-last').select2();
$('#filter-tahun-last').select2();
$('#filter-pengawas').select2();
let umpanbalikChartInstance = null;

function fetchChartData2(month = 'all', year = 'all', pengawas = 'all') {
    fetch(`{{ route('admin.chartData2') }}?bln=${month}&tahun=${year}&pengawas=${pengawas}`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                if (umpanbalikChartInstance) umpanbalikChartInstance.destroy();
                const ctx = document.getElementById('umpanbalikChart').getContext('2d');
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                ctx.font = '16px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('No data available for the chart', ctx.canvas.width / 2, ctx.canvas.height / 2);
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
                    datasets: [
                        {
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
  document.getElementById('export-pdf2').addEventListener('click', function () {
            const canvas = document.getElementById('umpanbalikChart');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-umpanbalikChart.pdf');
        });

// Initial chart load with no filters (all data)
fetchChartData2();

// Event listener for filter changes
$('#filter-bln-last, #filter-tahun-last , #filter-pengawas').change(function() {

    const pengawas = $('#filter-pengawas').val();
    const month = $('#filter-bln-last').val();
    const year = $('#filter-tahun-last').val();
    fetchChartData2(month, year,pengawas);
});

$('#filter-pengawas2').select2();

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
function fetchSpiderWebData(pengawas = 'all') {
    fetch(`{{ route('admin.spiderWebData') }}?pengawas=${pengawas}`)
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
                    labels: ['Kemampuan berinteraksi', 'Menciptakan Suasana', 'Penguasaan Materi', 'Kemampuan Komunikasi', 'Ketepatan Waktu'],
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
  document.getElementById('export-pdf5').addEventListener('click', function () {
            const canvas = document.getElementById('spiderWebPengawas');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-spiderWebPengawas.pdf');
        });

// Initial chart data load
fetchSpiderWebData();

// Fetch data when the pengawas filter changes
$('#filter-pengawas2').change(function() {
    const pengawas = $(this).val();
    fetchSpiderWebData(pengawas);
});


});



</script>
