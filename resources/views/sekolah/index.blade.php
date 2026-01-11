@extends('layouts.admin.home')
@section('title', 'List  Sekolah')
@section('titelcard', 'List  Sekolah')

@section ('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
          <div class="col-12">
              <div class="card mb-4">
                  <div class="card-header pb-0 p-3">
                      <div class="row">
                          <div class="col-6 d-flex align-items-center">
                              <h6 class="mb-0">Tabel  Sekolah</h6>
                          </div>
                          <div class="col-6 d-flex justify-content-end">
                            @if (Auth::user()->role == 'Super Admin')
                            <div class="btn-group" role="group" aria-label="Basic example">
                              <a  class="btn btn-sm bg-primary text-white " href="{{  route('sekolah.add')  }}"><i class="fas fa-plus" aria-hidden="true"></i> Add </a>
                              <a  class="btn btn-sm bg-info text-white" href="{{  route('sekolah.import')  }}" >  <i class="fa fa-file-excel-o" aria-hidden="true"></i> Import</a>
                              <a class="btn btn-sm  bg-success text-white " target="_blank" href="{{  route('sekolah.excelcontoh')  }}"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Contoh</a>
                           </div>
                           @endif
                          </div>
                      </div>
                  </div>
                  <div class="card-body">
                      @if (Session::has('success'))
                      <div class="alert alert-success">
                          {{ Session::get('success') }}
                      </div>
                      @endif
                      <div class="table-responsive p-0">
                        <table class="table" id="data-table">
                          <thead>
                            <tr>
                              <th class="text-sm font-weight mb-1 ">No</th>
                              <th class="text-sm font-weight mb-1 ">Nama Sekolah</th>
                              <th class="text-sm font-weight mb-1 ">NPSN</th>
                              <th class="text-sm font-weight mb-1">No Telpon</th>
                              <th class="text-sm font-weight mb-1">Alamat</th>
                              <th class="text-sm font-weight mb-1">Kota</th>
        
                              <th class="text-sm font-weight mb-1">Action</th>
        
                            </tr>
                          </thead>
                          <tbody>
                   
                          </tbody>
                        </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

@endsection
       @section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sekolah.getdata') }}",
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nama_sekolah', name: 'nama_sekolah'},
                {data: 'npsn', name: 'npsn'},
                {data: 'no_telp', name: 'no_telp'},

                {data: 'alamat_lengkap', name: 'alamat_lengkap'},
                {data: 'kota', name: 'kota'},

                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        // Handle delete button click with SweetAlert2
        jQuery('#data-table').on('click', '.deletePost', function(e) {
            e.preventDefault();
            var deleteUrl = jQuery(this).attr('href');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });

</script>

       @endsection

