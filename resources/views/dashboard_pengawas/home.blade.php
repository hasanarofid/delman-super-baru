@extends('layouts.pengawas.home')
@section('title', 'Profile')
@section('titelcard', 'Profile')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4>
      @php
      if(Auth::user()->foto_profile == 'userdefault.jpg'){
            $foto = asset('userdefault.jpg');
        }else{
            $foto =  route('fotopengawas',Auth::user()->foto_profile );
        }
      @endphp
      <!-- Header -->
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="user-profile-header-banner">
              <img src="{{ asset('theme/assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top" />
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
              <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                <img
                  src="{{ $foto }}"
                  alt="user image"
                  class="d-block ms-0 ms-sm-4 rounded user-profile-img" style="height: 100px !important" />
              </div>
              <div class="flex-grow-1 mt-3 mt-sm-5">
                <div
                  class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                  <div class="user-profile-info">
                    <h4>{{ Auth::user()->name}}</h4>
                    <ul
                      class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                      <li class="list-inline-item"><i class="ti ti-color-swatch"></i> {{ Auth::user()->jenjang_jabatan }}- {{  Auth::user()->pangkat}}</li>
                      <li class="list-inline-item"><i class="ti ti-map-pin"></i> {{ App\Kabupaten::find(Auth::user()->kabupaten_id)->nama_kabupaten }}</li>
                    </ul>
                  </div>
                  <a href="{{ route('pengawas.editprofile') }}" class="btn btn-primary">
                    <i class="ti ti-user-check me-1"></i>Edit Profile
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/ Header -->

      <!-- User Profile Content -->
      <div class="row">
        <div class="col-xl-12 col-lg-5 col-md-5">
          <!-- About User -->
          <div class="card mb-2">
            <div class="card-body">
              <small class="card-text text-uppercase">About</small>
              <ul class="list-unstyled mb-4 mt-3">
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-user"></i><span class="fw-bold mx-2">Nama Lengkap:</span> <span>{{ Auth::user()->name}}</span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-check"></i><span class="fw-bold mx-2">NIP:</span> <span>{{ Auth::user()->nip}}</span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-crown"></i><span class="fw-bold mx-2">Unit kerja:</span> <span> {{ Auth::user()->jenjang_jabatan }} - {{  Auth::user()->pangkat}} - {{ Auth::user()->gol_ruang }}</span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-flag"></i><span class="fw-bold mx-2">No Hp:</span> <span>
                    {{ App\Profile::where('user_id', Auth::user()->id)->first()->no_telp }}</span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-file-description"></i><span class="fw-bold mx-2">Homepage:</span>
                  <span>
                    @php
                    $profile = App\Profile::where('user_id', Auth::user()->id)->first();
                @endphp

                @if (!empty($profile->homepage))
                    <a href="{{ $profile->homepage }}" target="_blank">{{ $profile->homepage }}</a>
                @endif

             </span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-flag"></i><span class="fw-bold mx-2">Bio:</span> <span>
                    {{ !empty($profile->bio) ? $profile->bio : '' }}</span>
                </li>
              </ul>

            </div>
          </div>
        </div>
        <div class="col-xl-12 col-lg-10 col-md-10 mb-3">
          <h5 class="title">Kinerja</h5>

              {{-- chart --}}
              <div class="col-lg-12 mb-3">

                <div class="card">

                  <div class="card-header pb-0 p-1">
                    <button id="export-pdf" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                    <h6 class="mb-0">Grafik Jumlah Rencana 6 bulan terakhir </h6>
                  </div>
                      <div class="card-body p-3">

                          <canvas id="pengawasChart"></canvas> <!-- Canvas for the chart -->
                      </div>
                </div>
              </div>


              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header pb-0 p-1">
                    <button id="export-pdf2" class="btn btn-primary">Export PDF</button> <!-- Export button -->
                    <h6 class="mb-0">
                      Grafik Umpan Balik per Rencana Kerja
                       </h6>
                  </div>
                      <div class="card-body p-3">

                        <canvas id="umpanbalikChart"></canvas> <!-- Canvas for the chart -->
                      </div>
                </div>
            </div>



              {{-- end chart --}}

        </div>

        <div class="col-xl-12 col-lg-5 col-md-5">

          <!-- Profile Overview -->
          <div class="card mb-2">
            <div class="card-body">
              <p class="card-text text-uppercase">Rencana Kerja Bulan ini</p>
              <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-check"></i><span class="fw-bold mx-2"> Rencana:</span> <span> {{ $totalRencankerja }} </span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="ti ti-layout-grid"></i><span class="fw-bold mx-2">Sekolah yang dilayani:</span>
                  <span>{{ $sekolahdilayani }}</span>
                </li>
                <li class="d-flex align-items-center">
                  <i class="ti ti-users"></i><span class="fw-bold mx-2">List Rencana Kerja:</span>
                </li>
              </ul>
              <br>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Program</th>
                        <th>Total Sekolah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                          $no = 1;
                      @endphp
                      @foreach ($listsekolahdilayani as $item)
                      @php
                      $sekolahIds = explode(',', $item->sekolah_id);
                      $sekolahs = count($sekolahIds);

                @endphp
                      {{-- $sekolahIds = explode(',', $value->rencanakerja->sekolah_id);
                      $sekolahdilayani += count($sekolahIds); --}}
                      <tr>
                        <td> {{ $no++}} </td>
                        <td> {{ $item->nama_program_kerja}} </td>
                        <td>{{ $sekolahs }}</td>
                      </tr>
                      @endforeach
                    </tbody>


                  </table>
                </div>


            </div>
          </div>

          <div class="card mb-2">
            <div class="card-body">
              <p class="card-text text-uppercase">Daftar Sekolah Binaan Bulan Ini</p>
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sekolah</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $displayedSekolahIds = []; // Array untuk melacak sekolah_id yang sudah ditampilkan
                    @endphp
                    @foreach ($listsekolahdilayani as $item)
                        @php
                            $sekolahIds = explode(',', $item->sekolah_id);
                            $sekolahs = App\SekolahM::whereIn('id', $sekolahIds)->get();
                        @endphp
                        @foreach ($sekolahs as $sekolah)
                            @if (!in_array($sekolah->id, $displayedSekolahIds))
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $sekolah->nama_sekolah }}</td>
                                </tr>
                                @php
                                    $displayedSekolahIds[] = $sekolah->id; // Tandai sekolah_id sebagai sudah ditampilkan
                                @endphp
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            </div>
          </div>
          <!--/ Profile Overview -->
        </div>

      </div>
      <!--/ User Profile Content -->
    </div>
    <!-- / Content -->



    <div class="content-backdrop fade"></div>
  </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- jsPDF -->
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {


        $('#filter-bln').select2();
        $('#filter-tahun').select2();

        let pengawasChartInstance = null;

        function fetchChartData(month = 'all', year = 'all') {
        fetch(`{{ route('pengawas.chartData') }}?bln=${month}&tahun=${year}`)
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

                // Use labels and datasets from response data
                const labels = data.labels; // Nama bulan dalam bahasa Indonesia
                const rencanaCounts = data.datasets[0].data; // Data jumlah rencana

                // Destroy the existing chart instance if it exists
                if (pengawasChartInstance) {
                    pengawasChartInstance.destroy();
                }

                // Set up the chart and assign it to pengawasChartInstance
                const ctx = document.getElementById('pengawasChart').getContext('2d');
                pengawasChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels, // Nama bulan
                        datasets: [{
                            label: 'Jumlah Rencana Kerja',
                            data: rencanaCounts, // Data jumlah rencana
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
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
                                title: { display: true, text: 'Bulan' }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    document.getElementById('export-pdf').addEventListener('click', function () {
            const canvas = document.getElementById('pengawasChart');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-pengawasChart.pdf');
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
    // Jika year adalah 'all', gunakan tahun sekarang sebagai default
    const currentYear = new Date().getFullYear();
    const filterYear = (year === 'all') ? currentYear : year;
    fetch(`{{ route('pengawas.chartData2') }}?bln=${month}&tahun=${filterYear}&pengawas=${pengawas}`)
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

document.getElementById('export-pdf2').addEventListener('click', function () {
            const canvas = document.getElementById('umpanbalikChart');
            const pdf = new jspdf.jsPDF();

            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 10, 180, 90);
            pdf.save('chart-umpanbalikChart.pdf');
        });

// Initial chart load dengan tahun sekarang sebagai default
const currentYear2 = new Date().getFullYear();
fetchChartData2('all', currentYear2, 'all');


// Event listener for filter changes
$('#filter-bln-last, #filter-tahun-last').change(function() {

    const month = $('#filter-bln-last').val();
    let year = $('#filter-tahun-last').val();
    // Jika year adalah 'all', gunakan tahun sekarang sebagai default
    if (year === 'all') {
        year = new Date().getFullYear();
    }
    fetchChartData2(month, year, 'all');
});






});
</script>

