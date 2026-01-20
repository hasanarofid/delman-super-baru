@extends('layouts.admin.home')
@section('title', 'Import Pengawas')
@section('titelcard', 'Import Pengawas')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Import Pengawas</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <a href="{{ route('masterpengawas.index') }}" class="btn btn-secondary waves-effect waves-light">
                                    <i class="fas fa-arrow-left"></i>&nbsp;Kembali
                                </a>
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

                        <form id="importForm" action="{{ route('masterpengawas.importfile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Excel</label>
                                <input type="file" name="file" class="form-control" required>
                                <div class="form-text">Gunakan format yang sesuai dengan <a href="{{ route('masterpengawas.excelcontoh') }}">Contoh Export</a>.</div>
                            </div>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fas fa-file-import"></i>&nbsp;Import Sekarang
                            </button>
                        </form>
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
    document.getElementById('importForm').addEventListener('submit', function(e) {
        let timerInterval;
        Swal.fire({
            title: 'Sedang Memproses Import...',
            html: 'Mohon tunggu sebentar, data sedang dimasukkan ke sistem.<br><b></b>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                const b = Swal.getHtmlContainer().querySelector('b');
                timerInterval = setInterval(() => {
                    // b.textContent = Swal.getTimerLeft();
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        });
    });
</script>
@endsection
