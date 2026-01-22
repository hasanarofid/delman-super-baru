@extends('layouts.pengawas.home')
@section('title', 'Data Sekolah Binaan')
@section('titelcard', 'Data Sekolah Binaan')
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

      <div class="row">
        <div class="col-12 col-lg-12 ">
          <!-- About User -->
          <div class="card mb-4">
            <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
              <div class="app-card-header p-3 border-bottom-0">
                <div class="row align-items-center gx-3">


                  <div class="col-auto">
                    <h6 class="app-card-title">Sekolah Binaan <i>{{ Auth::user()->name}}</i> </h6>
                  </div>


                </div>
              </div>
              <div class="app-card-body px-4 w-100">
                <div class="table-responsive">
                  <table class="table table-bordered w-100" id="table-sekolah-binaan">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Sekolah</th>
                        <th>NISP/NPSN</th>
                        <th>Nama Kepala Sekolah</th>
                        <th>No Telp/whatsapp</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCenterTitle">Edit Sekolah Binaan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="editForm">
            @csrf
            <input type="hidden" name="id_sekolah" id="edit-id-sekolah">
            <div class="modal-body">
              <div class="row">
                <div class="col mb-3">
                  <label for="edit-npsn" class="form-label">NISP/NPSN Sekolah</label>
                  <input type="text" id="edit-npsn" name="npsn" class="form-control" placeholder="Masukkan NISP/NPSN"
                    required />
                </div>
              </div>
              <div class="row">
                <div class="col mb-3">
                  <label for="edit-kepala" class="form-label">Nama Kepala Sekolah</label>
                  <input type="text" id="edit-kepala" name="nama_kepala_sekolah" class="form-control"
                    placeholder="Masukkan Nama Kepala Sekolah" required />
                </div>
              </div>
              <div class="row">
                <div class="col mb-3">
                  <label for="edit-telp" class="form-label">No Telp/Whatsapp Kepala Sekolah</label>
                  <input type="text" id="edit-telp" name="no_telp" class="form-control" placeholder="Masukkan No Telp"
                    required />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection

  @section('script')
    <script>
      $(document).ready(function () {
        var table = $('#table-sekolah-binaan').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('pengawas.sekolahbinaan') }}",
          columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'nama_sekolah', name: 'nama_sekolah' },
            { data: 'npsn', name: 'npsn' },
            { data: 'nama_kepala_sekolah', name: 'nama_kepala_sekolah' },
            { data: 'no_telp', name: 'no_telp' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
          ],
          language: {
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
              "sFirst": "Pertama",
              "sPrevious": "Sebelumnya",
              "sNext": "Selanjutnya",
              "sLast": "Terakhir"
            }
          }
        });

        $(document).on('click', '.edit-btn', function () {
          var id = $(this).data('id');
          var npsn = $(this).data('npsn');
          var kepala = $(this).data('kepala');
          var telp = $(this).data('telp');

          $('#edit-id-sekolah').val(id);
          $('#edit-npsn').val(npsn);
          $('#edit-kepala').val(kepala);
          $('#edit-telp').val(telp);

          $('#editModal').modal('show');
        });

        $('#editForm').on('submit', function (e) {
          e.preventDefault();
          $('#saveBtn').prop('disabled', true).text('Menyimpan...');

          $.ajax({
            url: "{{ route('pengawas.sekolahbinaan.update') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
              $('#editModal').modal('hide');
              table.ajax.reload();
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.success,
                showConfirmButton: false,
                timer: 1500
              });
              $('#saveBtn').prop('disabled', false).text('Simpan');
            },
            error: function (xhr) {
              var errorMsg = 'Terjadi kesalahan';
              if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                errorMsg = '';
                $.each(errors, function (key, value) {
                  errorMsg += value[0] + '<br>';
                });
              } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
              }

              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: errorMsg
              });
              $('#saveBtn').prop('disabled', false).text('Simpan');
            }
          });
        });
      });
    </script>
  @endsection