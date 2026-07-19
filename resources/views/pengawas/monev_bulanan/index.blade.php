@extends('layouts.pengawas.home')
@section('title', 'Riwayat Laporan Monev Bulanan')
@section('titelcard', 'Laporan Monev Bulanan')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Daftar Laporan Monev Bulanan</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <a class="btn btn-primary waves-effect waves-light" href="{{ route('pengawas.monev-bulanan.create') }}">
                                    <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Buat Laporan Baru
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
                        <div class="table-responsive p-0">
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th>Nama Sekolah</th>
                                        <th>Total MoU</th>
                                        <th>Serapan BOSP</th>
                                        <th>Tanggal Submit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monevList as $index => $monev)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $monev->bulan }}</td>
                                        <td>{{ $monev->tahun }}</td>
                                        <td>{{ $monev->sekolah ? $monev->sekolah->nama_sekolah : '-' }}</td>
                                        <td>{{ $monev->total_mou }}</td>
                                        <td>{{ $monev->serapan_bosp }}%</td>
                                        <td>{{ $monev->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('pengawas.monev-bulanan.edit', $monev->id) }}" class="btn btn-sm btn-warning" title="Edit Laporan">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('pengawas.monev-bulanan.destroy', $monev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Laporan">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
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
<script>
   jQuery(document).ready(function() {
        jQuery('#data-table').DataTable();
   });
</script>
@endsection
