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
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <label for="filter-bln">Filter Bulan:</label>
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
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <label for="filter-status-tanggapan">Filter Status Tanggapan:</label>
                        <select
                            id="filter-status-tanggapan"
                            name="status_tanggapan"
                            class="select2 form-select"
                        >
                            <option value="all">All (Semua)</option>
                            <option value="belum">Belum diberi tanggapan</option>
                            <option value="sudah">Sudah diberi tanggapan</option>
                        </select>
                    </div>

                </div>

                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th><input type="checkbox" id="check-all-remind"></th>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pengawas</th>
                                <th>Sekolah </th>
                                <th>Kepala Sekolah </th>
                                <th>Program Kerja</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Total Kirim WA</th>
                                <th>Terakhir Kirim WA</th>
                                <th>Rencana Tindak Lanjut (RTL)</th>
                                <th>Catatan RTL</th>
                                <th>Aksi</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function kirimWaRemindSingle(id) {
      Swal.fire({
          title: 'Konfirmasi Kirim WA Remind',
          text: 'Apakah Anda yakin ingin mengirim ulang WA Remind untuk data ini?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, Kirim!',
          cancelButtonText: 'Batal'
      }).then((result) => {
          if (result.isConfirmed) {
              Swal.fire({
                  title: 'Memproses...',
                  text: 'Sedang mengantrikan pesan WA Remind...',
                  allowOutsideClick: false,
                  didOpen: () => {
                      Swal.showLoading();
                  }
              });

              $.ajax({
                  url: "{{ url('superadmin/listumpanbalik/kirim-wa-remind-single') }}/" + id,
                  type: "POST",
                  data: {
                      _token: "{{ csrf_token() }}"
                  },
                  success: function(res) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Berhasil!',
                          text: res.message || "Berhasil mengirim WA Remind!",
                          confirmButtonText: 'OK'
                      }).then(() => {
                          $('#dataTable').DataTable().ajax.reload(null, false);
                      });
                  },
                  error: function(xhr) {
                      var err = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan saat mengirim WA Remind.";
                      Swal.fire({
                          icon: 'error',
                          title: 'Gagal!',
                          text: err,
                          confirmButtonText: 'OK'
                      });
                  }
              });
          }
      });
  }

  $(document).ready(function () {

    $('#filter-pengawas').select2();
    $('#filter-bln').select2();
    $('#filter-tahun').select2();
    $('#filter-status-tanggapan').select2();

    $('#filter-pengawas, #filter-bln, #filter-tahun, #filter-status-tanggapan').change(function () {
        $('#dataTable').DataTable().ajax.reload();
    });

    $('#check-all-remind').on('click', function() {
        $('.check-remind-item').prop('checked', this.checked);
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
                d.status_tanggapan = $('#filter-status-tanggapan').val();
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'pengawas', name: 'pengawas'},
            {data: 'nama_sekolah', name: 'nama_sekolah'},
            {data: 'kepala_sekolah', name: 'kepala_sekolah'},
            {data: 'sasaran', name: 'sasaran'},
            {data: 'kategori', name: 'kategori'},
            {data: 'tanggapan_status', name: 'tanggapan_status', orderable: false, searchable: false},
            {data: 'total_kirim', name: 'total_kirim', orderable: false, searchable: false},
            {data: 'tgl_terakhir_kirim', name: 'tgl_terakhir_kirim', orderable: false, searchable: false},
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
        dom: 'Bfrtip',
        buttons: [
            {
                text: '<i class="fas fa-file-pdf"></i> Export PDF',
                className: 'btn btn-danger me-2',
                action: function ( e, dt, node, config ) {
                    var pengawas = $('#filter-pengawas').val();
                    var bln = $('#filter-bln').val();
                    var tahun = $('#filter-tahun').val();
                    var url = "{{ route('listumpanbalik.exportPDF') }}?pengawas=" + pengawas + "&bln=" + bln + "&tahun=" + tahun;
                    window.open(url, '_blank');
                }
            },
            {
                text: '<i class="fab fa-whatsapp"></i> Kirim WA Remind Massal (Terpilih)',
                className: 'btn btn-success',
                action: function ( e, dt, node, config ) {
                    var selectedIds = [];
                    $('.check-remind-item:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: "Pilih setidaknya satu data berstatus 'Belum diberi tanggapan' dengan mencentang kotak di tabel.",
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Kirim Massal',
                        text: "Kirim WA Remind massal ke " + selectedIds.length + " data terpilih?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim Massal!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang mengantrikan pesan WA Remind Massal...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: "{{ route('listumpanbalik.kirimWaRemindMasal') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    ids: selectedIds
                                },
                                success: function(res) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message || "Berhasil mengirim WA Remind Massal!",
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        $('#check-all-remind').prop('checked', false);
                                        $('#dataTable').DataTable().ajax.reload(null, false);
                                    });
                                },
                                error: function(xhr) {
                                    var err = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan saat mengirim WA Remind Massal.";
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: err,
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                }
            }
        ]
    });
  });
</script>

@endsection
