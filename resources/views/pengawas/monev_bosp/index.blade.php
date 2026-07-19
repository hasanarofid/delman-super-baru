@extends('layouts.pengawas.home')
@section('title', 'Riwayat Laporan Monev BOSP SMK')
@section('titelcard', 'Laporan Monev BOSP SMK')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Daftar Laporan Monev BOSP SMK</h6>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <a class="btn btn-primary waves-effect waves-light" href="{{ route('pengawas.monev-bosp.create') }}">
                                    <i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Buat Laporan Baru
                                </a>
                                &nbsp;
                                <a href="{{ route('pengawas.monev-bosp.export') }}" class="btn btn-success waves-effect waves-light">
                                    <i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel
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
                                        <th>Sekolah</th>
                                        <th>Bulan / Tahun</th>
                                        <th>Siswa Riil</th>
                                        <th>Siswa BOS</th>
                                        <th>Selisih</th>
                                        <th>Status Selisih</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($monevList as $data)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $data->sekolah->nama_sekolah ?? '-' }}</td>
                                        <td>{{ $data->bulan }} / {{ $data->tahun }}</td>
                                        <td>{{ $data->total_siswa_riil }}</td>
                                        <td>{{ $data->siswa_dinas_bos }}</td>
                                        <td>{{ abs($data->total_siswa_riil - $data->siswa_dinas_bos) }}</td>
                                        <td>
                                            @if($data->total_siswa_riil == $data->siswa_dinas_bos)
                                                <span class="badge bg-success">Sesuai</span>
                                            @elseif($data->total_siswa_riil > $data->siswa_dinas_bos)
                                                <span class="badge bg-warning">Kelebihan</span>
                                            @else
                                                <span class="badge bg-danger">Kekurangan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->file_sptjm)
                                                <a href="{{ asset('uploads/sptjm/' . $data->file_sptjm) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat SPTJM">
                                                    <i class="ti ti-file"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('pengawas.monev-bosp.edit', $data->id) }}" class="btn btn-sm btn-warning" title="Edit Laporan">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('pengawas.monev-bosp.destroy', $data->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
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
    $(document).ready(function() {
        $('#data-table').DataTable();
    });
</script>
@endsection
