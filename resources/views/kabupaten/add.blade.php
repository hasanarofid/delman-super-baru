@extends('layouts.admin.home')
@section('title', 'Add Kabupaten')
@section('titelcard', 'Add Kabupaten')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Add Kabupaten</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form class="row g-3" action="{{ route('kabupaten.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama_kabupaten">Nama Kabupaten</label>
                            <input type="text" class="form-control" name="nama_kabupaten" id="nama_kabupaten" placeholder="Nama Kabupaten" required>
                        </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">
                                <i class="fa fa-save"></i> Save
                            </button>
                            <a href="{{ route('kabupaten.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
