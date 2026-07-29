@extends('layouts.pengawas.home')
@section('title', 'Dashboard')
@section('titelcard', 'Dashboard')
@section('content')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row mt-4">
                <div class="col-12 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-0">Dashboard Kinerja Pengawas Sekolah</h4>
                                <p class="mb-0">Export semua grafik dan data kinerja dalam satu dokumen PDF</p>
                            </div>
                            <button id="export-full-pdf" class="btn btn-danger btn-lg shadow">
                                <i class="ti ti-file-download me-2"></i> Download Dashboard Kinerja (PDF)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Profile Charts Integrated --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Jumlah Rencana 6 Bulan Terakhir</h6>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="pengawasChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Umpan Balik per Rencana Kerja</h6>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="umpanbalikChart"></canvas>
                        </div>
                    </div>
                </div>
                {{-- End Profile Charts Integrated --}}
            </div>

            <div class="row mt-4">
                {{-- begin spider web --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Profil Kompetensi Pengawas </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf1" class="btn btn-primary">Export PDF</button> <!-- Export button -->

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
                            <button id="export-pdf2" class="btn btn-primary">Export PDF</button> <!-- Export button -->

                            <canvas id="piePengawas"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>
                {{-- end pie web --}}

            </div>

            <div class="row mt-4">
                <div class="col-lg-6 mb-3">
                    <div class="card" style="height: 100%;">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Jumlah Pengawasan Terkonfirmasi 6 bulan terakhir </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf3" class="btn btn-primary">Export PDF</button> <!-- Export button -->

                            <canvas id="chartKonfrim"></canvas> <!-- Canvas for the chart -->
                        </div>
                    </div>
                </div>

                {{-- begin rapot pendidikan chart --}}
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Grafik Jumlah Rencana Kerja per Raport Pendidikan </h6>
                        </div>
                        <div class="card-body p-3">
                            <button id="export-pdf4" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="filter-pengawas">Filter Bulan:</label>
                                    <select id="filter-bln2" name="bln" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($months as $month)
                                            <option value="{{ $month['name'] }}">
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-6">
                                    <label for="filter-tahun">Filter Tahun:</label>
                                    <select id="filter-tahun2" name="tahun" class="select2 form-select" required>
                                        <option value="all">All</option> <!-- Option to show all records -->
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $year == $currentYear ? 'selected' : '' }}>
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
                {{-- end rapot pendidikan chart --}}

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
            // chart 1
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
                fetch(`{{ route('pengawas.spiderWebData') }}?pengawas=${pengawas}`)
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

            document.getElementById('export-pdf1').addEventListener('click', function() {
                const canvas = document.getElementById('spiderWebPengawas');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-spiderWebPengawas.pdf');
            });
            // Initial chart data load
            fetchSpiderWebData();
            // end chart 1


            //chart terkonfirmasi
            $('#filter-bln3').select2();
            $('#filter-tahun3').select2();
            let terkomfrimChartInstance = null;

            function fetchChartTerkonfrim(month = 'all', year = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYear = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYear : year;
                fetch(`{{ route('pengawas.chartTerkonfirmasi') }}?bln=${month}&tahun=${filterYear}`)
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

                        // const pengawasNames = data.map(item => item.pengawas);
                        // const rencanaCounts = data.map(item => item.total);

                        const labels = data.labels; // Nama bulan dalam bahasa Indonesia
                        const rencanaCounts = data.datasets[0].data;

                        // Destroy the existing chart instance if it exists
                        if (terkomfrimChartInstance) {
                            terkomfrimChartInstance.destroy();
                        }

                        // Set up the chart and assign it to chartPerRencanaKerjaInstance
                        const ctx = document.getElementById('chartKonfrim').getContext('2d');
                        terkomfrimChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
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

            document.getElementById('export-pdf3').addEventListener('click', function() {
                const canvas = document.getElementById('chartKonfrim');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-chartKonfrim.pdf');
            });
            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear3 = new Date().getFullYear();
            fetchChartTerkonfrim('all', currentYear3);

            // Event listener for filter changes
            $('#filter-bln3, #filter-tahun3').change(function() {
                const month = $('#filter-bln3').val();
                let year = $('#filter-tahun3').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartTerkonfrim(month, year);
            });
            // chat 3

            // chart 2
            $('#filter-bln2').select2();
            $('#filter-tahun2').select2();
            let raportPendidikanChartInstance = null;

            function fetchChartDataRaportPendidikan(month = 'all', year = 'all') {
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                const currentYear = new Date().getFullYear();
                const filterYear = (year === 'all') ? currentYear : year;
                fetch(`{{ route('pengawas.chartDataRaportPendidikan') }}?bln=${month}&tahun=${filterYear}`)
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
            document.getElementById('export-pdf4').addEventListener('click', function() {
                const canvas = document.getElementById('chartPerRencanaKerja');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-chartPerRencanaKerja.pdf');
            });
            // Initial chart load dengan tahun sekarang sebagai default
            const currentYear2 = new Date().getFullYear();
            fetchChartDataRaportPendidikan('all', currentYear2);

            // Event listener for filter changes
            $('#filter-bln2, #filter-tahun2').change(function() {
                const month = $('#filter-bln2').val();
                let year = $('#filter-tahun2').val();
                // Jika year adalah 'all', gunakan tahun sekarang sebagai default
                if (year === 'all') {
                    year = new Date().getFullYear();
                }
                fetchChartDataRaportPendidikan(month, year);
            });

            // end chart 2


            // chart 4


            let pieChartInstance = null;

            // Fungsi untuk mengambil dan menampilkan data chart pie
            function fetchChartDataPie(pengawas = 'all') {
                fetch(`{{ route('pengawas.chartpie') }}?pengawas=${pengawas}`)
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

            document.getElementById('export-pdf2').addEventListener('click', function() {
                const canvas = document.getElementById('piePengawas');
                const pdf = new jspdf.jsPDF();

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
                pdf.save('chart-piePengawas.pdf');
            });
            // Load chart awal tanpa filter (semua data)
            fetchChartDataPie();

            // end chart 4


            // Dynamic Charts for Q1, Q2, Q4
            function fetchDynamicChart(questionId, canvasId, chartType, label) {
                fetch(`{{ route('pengawas.chartDynamicData') }}?question_id=${questionId}`)
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

            // ID 12: Pengembangan Profesional (Bar)
            fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional');
            // ID 14: Aspek Kompetensi (Pie)
            fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi');
            // ID 15: Kebermanfaatan (Pie)
            fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan');

            // NEW: Integrated Profile Charts Data Fetching
            let pengawasChartInstance = null;
            function fetchChartDataRencana() {
                fetch(`{{ route('pengawas.chartData') }}`)
                    .then(response => response.json())
                    .then(data => {
                        const ctx = document.getElementById('pengawasChart').getContext('2d');
                        pengawasChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Jumlah Rencana Kerja',
                                    data: data.datasets[0].data,
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: { responsive: true }
                        });
                    });
            }

            let umpanbalikChartInstance = null;
            function fetchChartDataUmpanbalik() {
                const currentYear = new Date().getFullYear();
                fetch(`{{ route('pengawas.chartData2') }}?bln=all&tahun=${currentYear}&pengawas=all`)
                    .then(response => response.json())
                    .then(data => {
                        const ctx = document.getElementById('umpanbalikChart').getContext('2d');
                        umpanbalikChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.map(item => item.rencana_kerja),
                                datasets: [
                                    {
                                        label: 'Respon Umpan Balik',
                                        data: data.map(item => item.total_respon),
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Umpan Balik Terkirim',
                                        data: data.map(item => item.total_umpan_balik),
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: { responsive: true, indexAxis: 'y' }
                        });
                    });
            }

            fetchChartDataRencana();
            fetchChartDataUmpanbalik();

            // Unified PDF Export Logic
            document.getElementById('export-full-pdf').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ti ti-loader rotate me-2"></i> Generating PDF...';
                btn.disabled = true;

                const canvases = {
                    'chart_rencana': 'pengawasChart',
                    'chart_umpanbalik': 'umpanbalikChart',
                    'chart_kompetensi': 'spiderWebPengawas',
                    'chart_realisasi': 'piePengawas',
                    'chart_terkonfirmasi': 'chartKonfrim',
                    'chart_raport': 'chartPerRencanaKerja',
                    'chart_q1': 'chartQ1',
                    'chart_q2': 'chartQ2',
                    'chart_q4': 'chartQ4'
                };

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('pengawas.exportDashboardKinerja') }}';
                form.target = '_blank';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                for (const [key, canvasId] of Object.entries(canvases)) {
                    const canvas = document.getElementById(canvasId);
                    if (canvas) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = canvas.toDataURL('image/png', 0.7); // 0.7 quality for size
                        form.appendChild(input);
                    }
                }

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 3000);
            });
        });
    </script>

    @if(isset($pesanStakeholder) && $pesanStakeholder)
    <!-- Modal Pop-Up Pesan Stakeholder -->
    <div class="modal fade" id="modalPesanStakeholder" tabindex="-1" aria-labelledby="modalPesanStakeholderLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white font-weight-bold" id="modalPesanStakeholderLabel">
                        <i class="fas fa-bullhorn me-2"></i>{{ $pesanStakeholder->judul }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3 bg-label-primary rounded-circle p-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-tie fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">{{ $pesanStakeholder->stakeholder ? $pesanStakeholder->stakeholder->name : 'Stakeholder / Kadis' }}</h6>
                            <small class="text-muted">Pesan Resmi Stakeholder &bull; {{ $pesanStakeholder->updated_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 bg-label-info p-3 mb-0" style="font-size: 1.05rem; line-height: 1.6;">
                        {!! nl2br(e($pesanStakeholder->isi_pesan)) !!}
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="btnUnderstandPesan" data-bs-dismiss="modal">
                        <i class="fas fa-check-circle me-1"></i> Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pesanId = "{{ $pesanStakeholder->id }}";
            var pesanUpdatedAt = "{{ $pesanStakeholder->updated_at }}";
            var storageKey = "stakeholder_pesan_read_" + pesanId + "_" + btoa(pesanUpdatedAt);

            if (!localStorage.getItem(storageKey)) {
                var modalEl = document.getElementById('modalPesanStakeholder');
                if (modalEl) {
                    var modalPesan = new bootstrap.Modal(modalEl);
                    modalPesan.show();
                }

                document.getElementById('btnUnderstandPesan')?.addEventListener('click', function () {
                    localStorage.setItem(storageKey, 'true');
                });
            }
        });
    </script>
    @endif

