@extends('layouts.admin.home')
@section('title', 'Edit Stakeholder')
@section('titelcard', 'Edit Stakeholder')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
       <div class="row">
         <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 p-3">
                     <div class="row">
                     <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">Edit Stakeholder</h6>
                     </div>
                     </div>
                  </div>
                    <div class="card-body">
                        @if (Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
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

                        <form class="row g-3" action="{{ route('stakeholder.update', ['id' => $models->id]) }}" method="POST" enctype="multipart/form-data">
                     @csrf
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Stakeholder</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ $models->name }}" placeholder="Nama Stakeholder" required>
                     </div>

                            <div class="col-md-6">
                                <label for="akses_kabupaten" class="form-label">Wilayah Kabupaten</label>
                        @php
                            $akses_kab = json_decode($models->akses_kabupaten, true) ?? [];
                        @endphp
                        <select name="akses_kabupaten[]" id="akses_kabupaten" class="form-control select2" multiple required>
                           <option value="All" {{ in_array('All', $akses_kab) ? 'selected' : '' }}>All Access</option>
                           @foreach ($wilayah as $item)
                                    <option value="{{ $item->id }}" {{ in_array($item->id, $akses_kab) ? 'selected' : '' }}>{{ $item->nama_kabupaten }}</option>
                           @endforeach
                            </select>
                     </div>

                            <div class="col-md-6">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nip" id="nip" value="{{ $models->nip }}" placeholder="NIP" readonly>
                     </div>

                            <div class="col-md-6">
                                <label for="akses_jenjang" class="form-label">Akses Jenjang</label>
                        @php
                            $akses_jen = json_decode($models->akses_jenjang, true) ?? [];
                        @endphp
                        <select name="akses_jenjang[]" id="akses_jenjang" class="form-control select2" multiple required>
                           <option value="All" {{ in_array('All', $akses_jen) ? 'selected' : '' }}>All Access</option>
                                    <option value="SMA" {{ in_array('SMA', $akses_jen) ? 'selected' : '' }}> SMA </option>
                                    <option value="SMK" {{ in_array('SMK', $akses_jen) ? 'selected' : '' }}> SMK </option>
                                    <option value="SKh" {{ in_array('SKh', $akses_jen) ? 'selected' : '' }}> SKh </option>
                        </select>
                     </div>

                            <div class="col-md-6">
                                <label for="pangkat" class="form-label">Pangkat</label>
                                <input type="text" class="form-control" name="pangkat" id="pangkat" value="{{ $models->pangkat }}" placeholder="Pangkat">
                     </div>

                            <div class="col-md-6">
                                <label for="gol_ruang" class="form-label">Gol. Ruang</label>
                                <input type="text" class="form-control" name="gol_ruang" id="gol_ruang" value="{{ $models->gol_ruang }}" placeholder="Gol. Ruang">
                     </div>

                            <div class="col-md-6">
                                <label for="no_telp" class="form-label">No WA</label>
                                <input type="number" class="form-control" name="no_telp" id="no_telp" value="{{ $models->no_telp }}" placeholder="No Telp/Wa" required>
                     </div>

                            <div class="col-md-6">
                                <label for="kota" class="form-label">Kota</label>
                                <input type="text" class="form-control" name="kota" id="kota" value="{{ $models->kota }}" placeholder="Kota">
                     </div>

                            <div class="col-12">
                                <label for="alamat_lengkap" class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat_lengkap" id="alamat_lengkap" rows="3" required>{{ $models->alamat_lengkap }}</textarea>
                     </div>

                            <div class="col-md-6">
                                <label for="kode_area" class="form-label">Kode Area</label>
                                <input type="number" class="form-control" name="kode_area" id="kode_area" value="{{ $models->kode_area }}" placeholder="Kode Area">
                     </div>

                            <div class="col-12">
                     <hr>
                                <h6 class="mb-3">Info Login (Kosongkan password jika tidak ingin mengubah)</h6>
                     </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="{{ $models->email }}" placeholder="Email" readonly>
                           </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password Baru">
                                <span class="input-group-text cursor-pointer toggle-password"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">
                                    <i class="fa fa-save"></i> Update
                        </button>
                                <a href="{{ route('stakeholder.index') }}" class="btn btn-secondary">Cancel</a>
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
<script>
    $(document).ready(function() {
        $('#akses_kabupaten').select2({
            placeholder: "Pilih Wilayah Kabupaten"
        });
        $('#akses_jenjang').select2({
            placeholder: "Pilih Akses Jenjang"
        });

        $('.toggle-password').click(function() {
            $(this).toggleClass('active');
            var input = $(this).parent().find('input');
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
                $(this).find('i').removeClass('ti-eye-off').addClass('ti-eye');
            } else {
                input.attr('type', 'password');
                $(this).find('i').removeClass('ti-eye').addClass('ti-eye-off');
            }
        });
    });
</script>
       @endsection
