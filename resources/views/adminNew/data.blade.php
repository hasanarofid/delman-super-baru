@extends('layouts.admin.home')
@section('title', 'list Admin')
@section('titelcard', 'list Admin')
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">Tabel Admin </h6>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="btn btn-primary waves-effect waves-light" href="{{ route('admin.add') }}"><i
                                                class="fas fa-plus" aria-hidden="true"></i>&nbsp;Admin</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body ">
                                @if (Session::has('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                    {{ Session::forget('success') }}
                                @endif
                                <div class="table-responsive p-0">
                                    <table class="table" id="data-table">
                                        <thead>
                                            <tr>
                                                <th class="text-sm font-weight mb-1 ">No</th>
                                                <th class="text-sm font-weight mb-1 ">Foto Profile</th>
                                                <th class="text-sm font-weight mb-1 ">Wilayah Kabupten</th>

                                                <th class="text-sm font-weight mb-1 ">Nama Admin</th>
                                                <th class="text-sm font-weight mb-1 ">Email</th>

                                                <th class="text-sm font-weight mb-1">No Whatsapp</th>
                                                <th class="text-sm font-weight mb-1">Alamat</th>

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
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        jQuery(document).ready(function() {
            jQuery('#myModal').on('show.bs.modal', function(event) {
                // Additional actions to perform when the modal is shown
                alert(1);
            });

            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var table = jQuery('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'foto',
                        name: 'foto'
                    },
                    {
                        data: 'kabupaten',
                        name: 'kabupaten'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
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
