@extends('layouts.admin.home')
@section('title', 'History WA Blast')
@section('titelcard', 'History WA Blast')
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
              <h5 class="m-0 me-2">Tabel History WA Blast</h5>
            </div>

          
          </div>
          <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
              
              <div class="row w-100 px-4 pt-3 pb-2 align-items-end">
                  <div class="col-md-4 mb-2 mb-md-0">
                      <label for="filter-status-history" class="form-label fw-bold">Filter Status WA:</label>
                      <select id="filter-status-history" class="form-select select2">
                          <option value="all">Semua Status</option>
                          <option value="belum_kirim">Belum Kirim WA Blast</option>
                          <option value="sudah_kirim">Sudah Kirim WA Blast</option>
                      </select>
                  </div>
                  <div class="col-md-8 d-flex justify-content-md-end justify-content-start">
                      @if(Auth::user() && Auth::user()->role == 'Super Admin')
                      <button type="button" id="btn-kirim-masal" class="btn btn-success me-2">
                          <i class="fa fa-paper-plane me-1"></i> Kirim WA Masal (<span id="selected-count">0</span> terpilih)
                      </button>
                      @endif
                  </div>
              </div>

              <div class="app-card-body px-4 w-100">
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                <th style="width: 30px; text-align: center;"><input type="checkbox" id="check-all"></th>
                                <th>No</th>
                                <th>Rencana Kerja</th>
                                <th>Kepala Sekolah</th>
                                <th>No Wa</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Action</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert2 -->


@section('script')


<script>

function kirimWaBlast(id,id_sekolah) {
    let button = $('#sendWaButton-' + id);  // Reference to the specific button

    // Disable button and add a loading state
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

    $.ajax({
        url: '{{ route("rencanatugas.kirimWaSekolah", ["id" => ":id", "id_sekolah" => ":id_sekolah"]) }}'.replace(':id', id).replace(':id_sekolah', id_sekolah),
        type: 'GET',
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message || 'Pesan WA telah masuk antrean.',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    }
                }
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            let errText = 'Failed to send WA message. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errText = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errText,
                confirmButtonText: 'OK'
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        }
    });
}

  $(document).ready(function () {
    $('#filter-status-history').select2();

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('wablasthistory.getdata') }}",
            data: function(d) {
                d.status = $('#filter-status-history').val();
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center'},
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'rencana', name: 'rencana'},
            {data: 'kepalasekolah', name: 'kepalasekolah'},
            {data: 'phone_number', name: 'phone_number'},
            {data: 'status', name: 'status'},
            {data: 'failure_reason', name: 'failure_reason'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#filter-status-history').change(function() {
        $('#check-all').prop('checked', false);
        updateSelectedCount();
        table.ajax.reload();
    });

    $('#check-all').on('click', function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    $(document).on('change', '.row-checkbox', function() {
        if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
            $('#check-all').prop('checked', true);
        } else {
            $('#check-all').prop('checked', false);
        }
        updateSelectedCount();
    });

    function updateSelectedCount() {
        let count = $('.row-checkbox:checked').length;
        $('#selected-count').text(count);
    }

    $('#btn-kirim-masal').on('click', function() {
        let selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih setidaknya satu data yang ingin dikirimkan WA!',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: 'Kirim WA Masal?',
            text: `Apakah Anda yakin ingin memasukkan ${selectedIds.length} data terpilih ke dalam antrean WA Blast?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let btn = $('#btn-kirim-masal');
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Memproses Antrean...');

                $.ajax({
                    url: '{{ route("wablasthistory.kirimmasal") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        log_ids: selectedIds
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Masuk Antrean Queue!',
                            text: response.message || 'Pesan WA telah dimasukkan ke dalam antrean queue.',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        }).then(() => {
                            $('#check-all').prop('checked', false);
                            updateSelectedCount();
                            table.ajax.reload(null, false);
                        });
                        btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-1"></i> Kirim WA Masal (<span id="selected-count">0</span> terpilih)');
                    },
                    error: function(xhr) {
                        let errMsg = xhr.responseJSON?.message || 'Gagal memproses kirim masal.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errMsg,
                            confirmButtonText: 'OK'
                        });
                        btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-1"></i> Kirim WA Masal (<span id="selected-count">' + selectedIds.length + '</span> terpilih)');
                    }
                });
            }
        });
    });
  });

</script>

@endsection
