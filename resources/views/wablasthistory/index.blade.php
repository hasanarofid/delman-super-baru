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
  
              
              <div class="app-card-body px-4 w-100">
                  <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
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
            // Show success message with SweetAlert and re-enable button
            Swal.fire({
                icon: 'success',
                title: 'Pesan Masuk Antrean',
                text: 'Mengikuti ketentuan terbaru dari WhatsApp, notifikasi Anda telah masuk antrean dan akan terkirim secara bertahap dalam kurun waktu 30 menit hingga 1 jam.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload DataTable after successful WA blast
                $('#dataTable').DataTable().ajax.reload();
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            // Show error message with SweetAlert and re-enable button
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to send WA message. Please try again.',
                confirmButtonText: 'OK'
            });
            button.prop('disabled', false).html('<i class="fa fa-envelope"></i> Kirim Wa');
        }
    });
}

  $(document).ready(function () {

   

    var table = $('#dataTable').DataTable({
     
        processing: true,
        serverSide: true,
        ajax: "{{ route('wablasthistory.getdata') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'rencana', name: 'rencana'},
            {data: 'kepalasekolah', name: 'kepalasekolah'},
            {data: 'phone_number', name: 'phone_number'},
            {data: 'status', name: 'status'},
            {data: 'failure_reason', name: 'failure_reason'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
  });


  
</script>

@endsection
