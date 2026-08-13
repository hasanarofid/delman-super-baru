@extends('layouts.pengawas.home')
@section('title', 'List Saran Perbaikan')
@section('titelcard', 'List Saran Perbaikan')
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
              <h5 class="m-0 me-2">Tabel Saran Perbaikan</h5>
            </div>
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
              <div class="app-card-body px-4 w-100">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                </div>
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th>No</th>
                                <th>Sekolah</th> 
                                <th>Saran Perbaikan</th>
                            </tr>
                          </thead>
                      </table>
                      <br>
                  </div>
                  <br>
              </div>
          </div>
      </div>
      <!--/ About User -->
  </div>
</div>
    <div class="content-backdrop fade"></div>
  </div>
@endsection

@section('script')
<script>
  $(document).ready(function () {
    if ($.fn.select2) {
        $('#filter-bln, #filter-tahun').select2();
    }

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('pengawas.saranperbaikan.getdata') }}",
            data: function(d) {
                d.bln = $('#filter-bln').val();
                d.tahun = $('#filter-tahun').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'saran_perbaikan', name: 'saran_perbaikan'},
        ],
    });

    $('#filter-bln, #filter-tahun').on('change', function () {
        table.ajax.reload();
    });
  });
</script>
@endsection
