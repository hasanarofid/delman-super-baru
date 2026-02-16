@extends('layouts.pengawas.home')
@section('title', 'List Dokumentasi Pendampingan')
@section('titelcard', 'List Dokumentasi Pendampingan')
@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      @if(Session::has('success'))
      <div class="alert alert-success">
          {{ Session::get('success') }}
      </div>
      {{ Session::forget('success') }}
      @endif

      <div class="col-12 col-lg-12">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Tabel Dokumentasi Pendampingan</h5>
            </div>
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
            <div class="app-card-body px-4 w-100">
              <div class="row mb-3">
            

                <div class="col-md-4">
                    <label for="filter-kategori">Filter Kategori Program:</label>
                    <select
                    id="filter-kategori"
                    name="kategori"
                    class="select2 form-select"
                    required
                >
                    <option value="all">All</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                    <option value="rhk3">Laporan RHK 3 (Mandiri / Refleksi Pengawas)</option>
                </select>
                </div>
                
                <div class="col-md-4">
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
                <div class="col-md-4">
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
            <div class="row mb-3">
              <div class="col-md-4">
                  <a href="#" id="downloadPDF" class="btn btn-danger">Download PDF</a>
              </div>
          </div>


              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Tanggal pendampingan</th>
                      <th>Foto Bukti Pendampingan</th>
                      <th>Sekolah</th>
                      <th>Program Kerja</th>
                      <th>Catatan RTL</th>
                    </tr>
                  </thead>
                </table>
                <br>
              </div>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function () {


    $('#downloadPDF').click(function (event) {
            event.preventDefault(); // Prevent the default link behavior

            // Get the selected filter values
            var pengawas = $('#filter-pengawas').val() || 'all';
            var bln = $('#filter-bln').val() || 'all';
            var tahun = $('#filter-tahun').val() || 'all';
            var searchQuery = $('#dataTable').DataTable().search();
            var url = "{{ route('pengawas.dokumentasipendampingan.exportPDF') }}";
            var bln = $('#filter-bln').val() || 'all';
            var tahun = $('#filter-tahun').val() || 'all';
            var kategori = $('#filter-kategori').val() || 'all';
            var searchQuery = $('#dataTable').DataTable().search();
            var url = "{{ route('pengawas.dokumentasipendampingan.exportPDF') }}";
    url += `?pengawas=${pengawas}&bln=${bln}&tahun=${tahun}&search=${searchQuery}&kategori=${kategori}`;
            console.log(url);

            // Open the constructed URL in a new tab
            window.open(url, '_blank');
        });


        $('#filter-bln').select2();
        $('#filter-bln').select2();
        $('#filter-tahun').select2();
        $('#filter-kategori').select2();

$('#filter-bln').change(function () {
    $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
});


$('#filter-tahun').change(function () {
    $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
});
$('#filter-kategori').change(function () {
    $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
});

    $('#dataTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
          url: "{{ route('pengawas.dokumentasipendampingan.getdata') }}",
          data: function(d) {
            d.bln = $('#filter-bln').val();
            d.tahun = $('#filter-tahun').val();
            d.kategori = $('#filter-kategori').val();
          }
        },
        columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'tanggal', name: 'tanggal'},
          // Kolom foto disembunyikan di DataTable, tapi tetap digunakan untuk ekspor PDF
          {data: 'foto', name: 'foto'},
          {data: 'nama_sekolah', name: 'nama_sekolah'},
          {data: 'program', name: 'program'},
          {data: 'catatan_rtl', name: 'catatan_rtl'},
        ]
      });
  });
</script>
@endsection
