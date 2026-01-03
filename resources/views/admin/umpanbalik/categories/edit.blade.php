@extends('layouts.admin.home')

@section('title', 'Edit Kategori Umpan Balik')
@section('titelcard', 'Edit Kategori Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Kategori Umpan Balik</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('umpanbalik.categories.update', $umpanbalikCategory->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $umpanbalikCategory->name) }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $umpanbalikCategory->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input @error('status') is-invalid @enderror" id="status" name="status" value="1" {{ old('status', $umpanbalikCategory->status) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Aktif</label>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('umpanbalik.categories.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection