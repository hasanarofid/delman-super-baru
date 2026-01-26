@extends('layouts.admin.home')

@section('title', 'Pertanyaan Umpan Balik')
@section('titelcard', 'Pertanyaan Umpan Balik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pertanyaan Umpan Balik</h5>
            @if(Auth::user()->role != 'Stakeholder')
            <a href="{{ route('umpanbalik.questions.create') }}" class="btn btn-primary btn-sm float-right">Tambah Pertanyaan</a>
            @endif
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
                            @if(Auth::user()->role != 'Stakeholder')
                            <th>Aksi</th>
                            @endif
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
                            @if(Auth::user()->role != 'Stakeholder')
                            <td>
                                <a href="{{ route('umpanbalik.questions.show', $question->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('umpanbalik.questions.edit', $question->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('umpanbalik.questions.destroy', $question->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-button">Hapus</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
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
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
