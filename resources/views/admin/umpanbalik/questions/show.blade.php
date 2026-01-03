@extends('layouts.admin.home')

@section('title', 'Detail Pertanyaan Umpan Balik')
@section('titelcard', 'Detail Pertanyaan Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detail Pertanyaan Umpan Balik</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Kategori:</label>
                <p>{{ $umpanbalikQuestion->category->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">Pertanyaan:</label>
                <p>{{ $umpanbalikQuestion->pertanyaan }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipe Input:</label>
                <p>{{ $umpanbalikQuestion->type_input }}</p>
            </div>

            @if ($umpanbalikQuestion->options)
                <div class="mb-3">
                    <label class="form-label">Opsi Jawaban:</label>
                    <ul>
                        @foreach ($umpanbalikQuestion->options as $option)
                            <li>{{ $option }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Status:</label>
                <p>
                    @if ($umpanbalikQuestion->status)
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-danger">Tidak Aktif</span>
                    @endif
                </p>
            </div>

            <div class="mb-3">
                <label class="form-label">Urutan:</label>
                <p>{{ $umpanbalikQuestion->urutan }}</p>
            </div>

            <a href="{{ route('umpanbalik.questions.index') }}" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>
@endsection