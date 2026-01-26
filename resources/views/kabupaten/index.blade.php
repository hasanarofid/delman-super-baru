@extends('layouts.admin.home')
@section('title', 'Master Kabupaten')
@section('titelcard', 'Master Kabupaten')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Tabel Kabupaten</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                @if (Auth::user()->role == 'Super Admin')
                                <a class="btn btn-primary waves-effect waves-light" href="{{ route('kabupaten.add') }}">
                                    <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add
                                </a>
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
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kabupaten</th>
                                        <th>Action</th>
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
   jQuery(document).ready(function() {
    var table = jQuery('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('kabupaten.getdata') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_kabupaten', name: 'nama_kabupaten'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    jQuery('#data-table').on('click', '.deletePost', function(e) {
        e.preventDefault();
        var deleteUrl = jQuery(this).attr('href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
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

