@extends('layouts.pengawas.home')
@section('title', 'List Perencanaan')
@section('titelcard', 'List Perencanaan')
@section('css')
    <style>
        /* Premium Select2 Styling */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #dbdade !important;
            border-radius: 0.375rem !important;
            height: auto !important;
            min-height: 38px !important;
            padding: 0.2rem 0.5rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #7367f0 !important;
            box-shadow: 0 0.125rem 0.25rem rgba(115, 103, 240, 0.4) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #5d596c !important;
            line-height: 1.5 !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-dropdown {
            border: 1px solid #dbdade !important;
            box-shadow: 0 0.25rem 1rem rgba(165, 163, 174, 0.45) !important;
            border-radius: 0.375rem !important;
            z-index: 10000 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #dbdade !important;
            border-radius: 0.375rem !important;
            padding: 6px 10px !important;
            margin-bottom: 5px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(115, 103, 240, 0.08) !important;
            color: #7367f0 !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #7367f0 !important;
            color: #fff !important;
        }

        /* Fix for the white box/highlight issue in the screenshot */
        .select2-search--dropdown {
            padding: 0.5rem !important;
        }

        .select2-results__option {
            padding: 8px 12px !important;
        }

        /* Remove the annoying focus highlight if that's what they meant */
        .select2-container:focus {
            outline: none !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 95% !important;
                margin: 10px auto !important;
            }
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="col-12 col-lg-12 ">
                <!-- About User -->
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Tabel Input Rencana Kerja</h5>
                            <small class="text-muted">Pengawas : {{ Auth::user()->name}}</small>
                        </div>

                        <div>
                            <a href="{{ route('pengawas.perencanaan.exportPDF') }}" class="btn btn-sm bg-danger text-white">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i> Download PDF
                            </a>
                            <a class="btn btn-sm bg-primary text-white" data-bs-toggle="modal" data-bs-target="#editUser"><i
                                    class="fas fa-plus" aria-hidden="true"></i> Tambah </a>
                        </div>

                    </div>
                    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">


                        <div class="app-card-body px-4 w-100">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Bulan - Tahun</th>
                                            <th>Nama Program Kerja</th>
 
                                            <th>Jenis Program</th>
                                            <th>Aspek Raport Pendidikan</th>
                                            <th>Sekolah Sasaran</th>
                                            <th>Deskripsi Alasan</th>
                                            <th>Status WA</th>
                                            <th>Tanggal Dibuat</th>
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
    @include('dashboard_pengawas.perencanaan.modal')
@endsection
@include('dashboard_pengawas.perencanaan.js')