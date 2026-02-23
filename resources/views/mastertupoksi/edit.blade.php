@extends('layouts.admin.home')
@section('title', 'Edit  Kategori Program')
@section('titelcard', 'Edit  Kategori Program')

@section ('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
          <div class="col-12">
              <div class="card mb-4">
                  <div class="card-header pb-0 p-3">
                      <div class="row">
                          <div class="col-6 d-flex align-items-center">
                              <h6 class="mb-0">Edit  Kategori Program</h6>
                          </div>
                     
                      </div>
                  </div>
                  <div class="card-body">
                     @if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
    {{ Session::forget('success') }}
@endif
              
               @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form class="row g-3" action="{{ route('mastertupoksi.update',array('id'=>$models->id)) }}"
    method="POST"
    enctype="multipart/form-data">
                   
                     @csrf
                     <div class="form-group mb-3">
                              <label for="nama">Nama </label>
                              <input type="text" required class="form-control" value="{{ $models->nama }}" name="nama" id="nama" placeholder="Nama" required>
                     </div>
                     <div class="form-group mb-3">
                              <label for="status">Status </label>
                              <select name="status" id="status" class="form-select" required>
                                  <option value="active" {{ $models->status == 'active' ? 'selected' : '' }}>Active</option>
                                  <option value="inactive" {{ $models->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                              </select>
                     </div>
               
                     <div class="form-group">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1"> <i class="fa fa-save"></i>   Save</button>
                    </div>
                    
                  </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

@endsection
       @section('script')


       @endsection



