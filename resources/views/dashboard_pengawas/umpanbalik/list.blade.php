@extends('layouts.pengawas.home')
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
                                <th>Status Tanggapan</th>
                                <th>Rencana Tindak Lanjut (RTL)</th>
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

    $('#filter-bln').select2();
        $('#filter-tahun').select2();
        var isExporting = false;


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
                url: "{{ route('pengawas.listumpanbalik.getdata') }}",
                data: function(d) {
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
                name: 'rtl_action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.is_rtl == 1) {
                        var rtlDate = row.tgl_rtl ? `<br><small class="text-muted">(${row.tgl_rtl})</small>` : '';
                        return `<span class="badge bg-label-success">Sudah ditindak lanjuti</span>${rtlDate}`;
                    } else {
                        return `<button type="button" class="btn btn-sm btn-outline-danger rtl-btn" data-id="${row.id}">Belum ditindak lanjuti</button>`;
                    }
                }
            },
            {
                data: 'catatan_rtl',
                name: 'catatan_rtl',
                render: function(data) {
                    return data ? data : '-';
                }
            }
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

    $(document).on('click', '.rtl-btn', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Konfirmasi RTL',
            html: `
                <div class="text-start">
                    <p>Apakah Anda yakin ingin menandai RTL ini?</p>
                    <label class="form-label">Catatan Rencana Tindak Lanjut (Opsional):</label>
                    <textarea id="catatan_rtl" class="form-control" rows="3" placeholder="Tulis catatan rencana di sini..."></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                return document.getElementById('catatan_rtl').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var catatan = result.value;
                $.ajax({
                    url: "{{ route('pengawas.updateRTL') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        is_rtl: 1,
                        catatan_rtl: catatan
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Berhasil!',
                                'RTL dan catatan berhasil disimpan.',
                                'success'
                            );
                            $('#dataTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat memperbarui RTL.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat berkomunikasi dengan server.',
                            'error'
                        );
                    }
                });
            }
        });
    });
  });



</script>

@endsection

