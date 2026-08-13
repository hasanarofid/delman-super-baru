@extends('layouts.admin.home')
@section('title', 'List Layanan yang dibutuhkan')
@section('titelcard', 'List Layanan yang dibutuhkan')
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
    <div class="col-12 col-lg-12 ">
      <!-- About User -->
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Tabel Layanan yang dibutuhkan</h5>
            </div>
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
              <div class="app-card-body px-4 w-100">
                <div class="row mb-3 align-items-end">
                  <div class="col-md-3">
                      <label for="filter-pengawas" class="form-label fw-bold">Filter by Pengawas:</label>
                      <select id="filter-pengawas" name="pengawas" class="select2 form-select" required>
                          <option value="all">All</option>
                          @foreach ($listPengawas as $item)
                              <option value="{{ $item->id }}">{{ $item->name.' - '.$item->nip }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-3">
                      <label for="filter-bln" class="form-label fw-bold">Filter Bulan:</label>
                      <select id="filter-bln" name="bln" class="select2 form-select" required>
                          <option value="all">Semua Bulan</option>
                          @foreach($months as $month)
                              <option value="{{ $month['name'] }}">
                                  {{ $month['name'] }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-3">
                      <label for="filter-tahun" class="form-label fw-bold">Filter Tahun:</label>
                      <select id="filter-tahun" name="tahun" class="select2 form-select" required>
                          <option value="all">Semua Tahun</option>
                          @foreach($years as $year)
                              <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                  {{ $year }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-3">
                      <a href="#" id="downloadPDF" class="btn btn-danger w-100">Download PDF</a>
                  </div>
                </div>
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th>No</th>
                                <th>Pengawas</th>
                                <th>Sekolah</th> 
                                <th>Layanan yang dibutuhkan</th>
                            </tr>
                          </thead>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function () {
    if ($.fn.select2) {
        $('#filter-pengawas, #filter-bln, #filter-tahun').select2();
    }

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('layanandibutuhkan.getdata') }}",
            data: function(d) {
                d.pengawas = $('#filter-pengawas').val();
                d.bln = $('#filter-bln').val();
                d.tahun = $('#filter-tahun').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'pengawas', name: 'pengawas'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'layanan', name: 'layanan'},
        ],
    });

    $('#filter-pengawas, #filter-bln, #filter-tahun').on('change', function () {
        table.ajax.reload();
    });

    $('#downloadPDF').click(function (event) {
        event.preventDefault(); 
        var pengawas = $('#filter-pengawas').val() || 'all';
        var bln = $('#filter-bln').val();
        var tahun = $('#filter-tahun').val();
        var searchQuery = table.search();
        var url = "{{ route('layanandibutuhkan.exportPDF') }}";
        url += `?pengawas=${encodeURIComponent(pengawas)}&bln=${encodeURIComponent(bln)}&tahun=${encodeURIComponent(tahun)}&search=${encodeURIComponent(searchQuery)}`;
        window.open(url, '_blank');
    });
  });
</script>
@endsection
