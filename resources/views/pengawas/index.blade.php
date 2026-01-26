@extends('layouts.admin.home')
@section('title', 'List Pengawas')
@section('titelcard', 'List Pengawas')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Tabel Pengawas</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                @if (Auth::user()->role == 'Super Admin')
                                <div class="btn-group" role="group">
                                    <a class="btn btn-primary waves-effect waves-light" href="{{ route('masterpengawas.add') }}">
                                        <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add
                                    </a>
                                    <a class="btn btn-success waves-effect waves-light" href="{{ route('masterpengawas.export') }}">
                                        <i class="fas fa-file-excel" aria-hidden="true"></i>&nbsp;Export
                                    </a>
                                    <a class="btn btn-secondary waves-effect waves-light" href="{{ route('masterpengawas.exportDelmanSuper') }}" style="background-color: #28a745; border-color: #28a745;">
                                        <i class="fas fa-file-excel" aria-hidden="true"></i>&nbsp;Export Delman Super
                                    </a>
                                    <a class="btn btn-info waves-effect waves-light" href="{{ route('masterpengawas.import') }}">
                                        <i class="fas fa-file-import" aria-hidden="true"></i>&nbsp;Import
                                    </a>
                                    <a class="btn btn-warning waves-effect waves-light" href="{{ route('masterpengawas.excelcontoh') }}">
                                        <i class="fas fa-file-download" aria-hidden="true"></i>&nbsp;Contoh Export
                                    </a>
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
                        @if (Session::has('error'))
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                        @endif
                        <div class="table-responsive p-0">
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th >No</th>
                                        <th >Foto Profile</th>
                                        <th >Kabupten</th>

                                        <th >Nama Pengawas</th>
                                        <th >NIP</th>
                                        <th >Jenjang Jabatan</th>
                                        <th >Pangkat</th>
                                        <th >Gol. Ruang</th>
                                        <th >No Whatsapp</th>
                                        <th >Alamat</th>
                                        <th >Sekolah Binaan</th>
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

<!-- Modal Update Kabupaten -->
<div class="modal fade" id="updateKabupatenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Kabupaten Pengawas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateKabupatenForm">
                @csrf
                <input type="hidden" name="id" id="pengawas_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kabupaten_id" class="form-label">Pilih Kabupaten</label>
                        <select class="form-select select2" name="kabupaten_id" id="kabupaten_id_select" required>
                            <option value="">-- Pilih Kabupaten --</option>
                            @php
                                $allKabupaten = \App\Kabupaten::all();
                            @endphp
                            @foreach($allKabupaten as $kab)
                                <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
   jQuery(document).ready(function() {
    
    // Initialize Select2 in Modal
    $('#updateKabupatenModal').on('shown.bs.modal', function () {
        $('#kabupaten_id_select').select2({
            dropdownParent: $('#updateKabupatenModal'),
            width: '100%'
        });
    });

    var table = jQuery('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('masterpengawas.getdata') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'foto', name: 'foto'},
            {data: 'kabupaten', name: 'kabupaten'},

            {data: 'name', name: 'name'},
            {data: 'nip', name: 'nip'},
            {data: 'jenjang_jabatan', name: 'jenjang_jabatan'},
            {data: 'pangkat', name: 'pangkat', render: function(data, type, row) {
                return (data !== null && data !== '0' && data !== '') ? data : '-';
            }},
            {data: 'gol_ruang', name: 'gol_ruang', render: function(data, type, row) {
                return (data !== null && data !== '0' && data !== '') ? data : '-';
            }},
            {data: 'no_telp', name: 'no_telp'},
            {data: 'alamat', name: 'alamat'},
            {data: 'binaan', name: 'binaan'},

            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    // Handle Update Kabupaten button click
    jQuery('#data-table').on('click', '.updateKabupatenBtn', function() {
        var id = $(this).data('id');
        var kabupatenId = $(this).data('kabupaten-id');
        
        $('#pengawas_id').val(id);
        $('#kabupaten_id_select').val(kabupatenId).trigger('change');
        $('#updateKabupatenModal').modal('show');
    });

    // Handle Update Kabupaten form submission
    $('#updateKabupatenForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: "{{ route('masterpengawas.updateKabupaten') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#updateKabupatenModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                    });
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengupdate data.',
                });
            }
        });
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
