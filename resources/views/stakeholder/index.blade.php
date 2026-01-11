@extends('layouts.admin.home')
@section('title', 'List Stakeholder')
@section('titelcard', 'List Stakeholder')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Tabel Stakeholder</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <a class="btn btn-primary waves-effect waves-light" href="{{ route('stakeholder.add') }}">
                                        <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add Stakeholder
                                    </a>
                                </div>
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
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead>
                                    <tr>
                                      <th >No</th>
                                      <th >Foto Profile</th>
                                      <th >Kabupten</th>
                
                                      <th >Nama</th>
                                      <th >Email</th>
                
                                      <th >No Whatsapp</th>
                                      <th >Alamat</th>
                                      <th >Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
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
            ajax: "{{ route('stakeholder.getdata') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'foto', name: 'foto'},
                {data: 'kabupaten', name: 'kabupaten'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'no_telp', name: 'no_telp'},
                {data: 'alamat', name: 'alamat'},
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
