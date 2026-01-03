@extends('layouts.admin.home')

@section('title', 'Pertanyaan Umpan Balik')
@section('titelcard', 'Pertanyaan Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pertanyaan Umpan Balik</h5>
            <a href="{{ route('umpanbalik.questions.create') }}" class="btn btn-primary btn-sm float-right">Tambah Pertanyaan</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Pertanyaan</th>
                            <th>Tipe Input</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $question->category->name ?? 'N/A' }}</td>
                            <td>{{ $question->pertanyaan }}</td>
                            <td>{{ $question->type_input }}</td>
                            <td>
                                @if ($question->status)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>{{ $question->urutan }}</td>
                            <td>
                                <a href="{{ route('umpanbalik.questions.show', $question->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('umpanbalik.questions.edit', $question->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('umpanbalik.questions.destroy', $question->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">Hapus</button>
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
@endsection