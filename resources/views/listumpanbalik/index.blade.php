@extends('layouts.admin.home')
@section('title', 'List Umpan Balik')
@section('titelcard', 'List Umpan Balik')
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
              <h5 class="m-0 me-2">Tabel Umpan Balik</h5>
            </div>


          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">


              <div class="app-card-body px-4 w-100">

                <div class="row mb-3">
                    <div class="col-md-4">
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

                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pengawas</th>
                                <th>Sekolah </th>
                                <th>Kepala Sekolah </th>
                                <th>Program Kerja</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Rencana Tindak Lanjut (RTL)</th>
                                <th>Catatan RTL</th>
                                <th>Preview</th>
                                {{-- <th>#</th> --}}
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

      <!--/ Profile Overview -->
  </div>
</div>




    <div class="content-backdrop fade"></div>
  </div>
@endsection


@section('script')


<script>
  $(document).ready(function () {

    $('#filter-pengawas').select2();
        $('#filter-bln').select2();
        $('#filter-tahun').select2();
        var isExporting = false;
        $('#filter-pengawas').change(function () {
            $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
        });


        $('#filter-bln').change(function () {
            $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
        });


        $('#filter-tahun').change(function () {
            $('#dataTable').DataTable().ajax.reload(); // Reload the table when filter changes
        });




 $('#dataTable').DataTable({

        processing: true,
        serverSide: true,
        ajax: {
                url: "{{ route('listumpanbalik.getdata') }}",
                data: function(d) {
                         d.pengawas = $('#filter-pengawas').val();
                         d.bln = $('#filter-bln').val();
                         d.tahun = $('#filter-tahun').val();
                 }
            },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'pengawas', name: 'pengawas'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'kepala_sekolah', name: 'kepala_sekolah'},
            {data: 'sasaran', name: 'sasaran'},
            {data: 'kategori', name: 'kategori'},
            {data: 'tanggapan_status', name: 'tanggapan_status', orderable: false, searchable: false},
            {
                data: null,
                name: 'rtl_status',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var rtlStatus = row.is_rtl == 1 ? 'Sudah dilakukan' : 'Belum dilakukan';
                    var rtlDate = row.tgl_rtl ? ` (${row.tgl_rtl})` : '';
                    return `${rtlStatus}${rtlDate}`;
                }
            },
            {
                data: 'catatan_rtl',
                name: 'catatan_rtl',
                render: function(data) {
                    return data ? data : '-';
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
            dom: 'Bfrtip', // Enables the buttons at the top of the DataTable
            buttons: [
                {
                    extend: 'pdfHtml5',
                    title: 'List Umpan Balik',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    },
                    customize: function (doc) {
                        doc.styles.tableHeader.alignment = 'left';
                    }
                }
            ]
    });
  });



</script>

@endsection
