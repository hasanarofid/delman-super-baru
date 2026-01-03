@extends('layouts.admin.home')

@section('title', 'Detail Kategori Umpan Balik')
@section('titelcard', 'Detail Kategori Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detail Kategori Umpan Balik</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nama Kategori:</label>
                <p>{{ $umpanbalikCategory->name }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi:</label>
                <p>{{ $umpanbalikCategory->description }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">Status:</label>
                <p>
                    @if ($umpanbalikCategory->status)
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-danger">Tidak Aktif</span>
                    @endif
                </p>
            </div>

            <a href="{{ route('umpanbalik.categories.index') }}" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>
@endsection