@extends('layouts.pengawas.home')
@section('title', 'Dashboard')
@section('titelcard', 'Dashboard')
@section('content')
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">

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
                            <h6 class="mb-0"> Realisasi Pelaksanaan Pendampingan </h6>
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
                            <h6 class="mb-0">Grafik Jumlah Pendampingan Terkonfirmasi 6 bulan terakhir </h6>
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
                fetch(`{{ route('pengawas.chartTerkonfirmasi') }}?bln=${month}&tahun=${year}`)
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
                                        title: {
                                            display: true,
                                            text: 'Jumlah Pendampingan'
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
            fetchChartTerkonfrim();

            // Event listener for filter changes
            $('#filter-bln3, #filter-tahun3').change(function() {
                const month = $('#filter-bln3').val();
                const year = $('#filter-tahun3').val();
                fetchChartTerkonfrim(month, year);
            });
            // chat 3

            // chart 2
            $('#filter-bln2').select2();
            $('#filter-tahun2').select2();
            let raportPendidikanChartInstance = null;

            function fetchChartDataRaportPendidikan(month = 'all', year = 'all') {
                fetch(`{{ route('pengawas.chartDataRaportPendidikan') }}?bln=${month}&tahun=${year}`)
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
            fetchChartDataRaportPendidikan();

            // Event listener for filter changes
            $('#filter-bln2, #filter-tahun2').change(function() {
                const month = $('#filter-bln2').val();
                const year = $('#filter-tahun2').val();
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

        });
    </script>
