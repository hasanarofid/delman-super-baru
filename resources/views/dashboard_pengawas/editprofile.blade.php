@extends('layouts.pengawas.home')
@section('title','Login')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Edit Profile</h4>
      @if(Session::has('success'))
      <div class="alert alert-success">
          {{ Session::get('success') }}
      </div>
      {{ Session::forget('success') }}
  @endif
     

      <!-- User Profile Content -->
      <div class="row">
        <div class="col-12 col-lg-6 ">
          <!-- About User -->
          <div class="card mb-4">
            <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
                <div class="app-card-header p-3 border-bottom-0">
                    <div class="row align-items-center gx-3">
               

                        <div class="col-auto">
                            <h4 class="app-card-title">Profile: {{ $user->name }}</h4>
                        </div>

                    </div>
                </div>
                @php
                if(Auth::user()->foto_profile == 'userdefault.jpg'){
                      $foto = asset('userdefault.jpg');
                  }else{
                      $foto =  route('fotopengawas',$user->foto_profile );
                  }
                @endphp
                <div class="app-card-body px-4 w-100">
                    <div class="item py-3">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-auto">
                                <div class="item-label "><strong>Photo Profile</strong></div>
                                <div class="flex-shrink-0  mx-sm-0 mx-auto">
                                    <img
                                      src="{{ $foto }}"
                                      alt="user image"
                                      class="d-block ms-0 ms-sm-4 rounded user-profile-img" style="height: 100px !important" />
                                  </div>

                            </div>                               
                                                      
                        </div>
                    </div>
                    <div class="item mb-3">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-auto">
                                <div class="item-data">{{ $user->name }}</div>
                                <div class="item-data">{{  $user->pangkat}}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="item border-bottom py-3">
                        <form action="{{ route('pengawas.updateprofile') }}"
                        method="POST"
                        enctype="multipart/form-data" class="settings-form">
                        @csrf
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-upload-file">Ubah Foto Profile</label>
                                <input type="file" name="foto" id="foto" class="form-control">
                              </div>

                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->name }}" required="">
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $user->profile->alamat_lengkap }}" required="">
                                </div>
                                <div class="mb-3">
                                    <label for="telp" class="form-label">No Telp</label>
                                    <input type="number" class="form-control" id="telp" name="telp" value="{{ $user->profile->no_telp }}" required="">
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Homepage</label>
                                    <input type="text" class="form-control" id="homepage" name="homepage" value="{{ $user->profile->homepage }}" required="">
                                </div>
                                <div class="mb-3">
                                    <label for="telp" class="form-label">Profile Singkat (Maks: 1000 Karakter)</label>
                                    <textarea class="form-control" id="profile" maxlength="1000" name="profile" style="min-height:100px">
                                        {{ $user->profile->bio }}
                                    </textarea>
                                </div>
                                <button type="submit" class="btn btn-primary button-profile">Ubah Profile</button>
                            </form>
                    </div>
                </div>		

            </div>
          </div>
          <!--/ About User -->

          <!--/ Profile Overview -->
        </div>
        <div class="col-12 col-lg-6">
          <!-- Activity Timeline -->
          <div class="card ">
            <div class="card-header align-items-center">
              <h5 class="card-action-title mb-0">Ubah Password</h5>
            </div>
            <div class="card-body ">

                @if(Session::has('success_pass'))
      <div class="alert alert-success">
          {{ Session::get('success_pass') }}
      </div>
      {{ Session::forget('success_pass') }}
  @endif

  @if(Session::has('error_pass'))
      <div class="alert alert-danger">
          {{ Session::get('error_pass') }}
      </div>
      {{ Session::forget('error_pass') }}
  @endif

                <form id="formUpdp"
                action="{{ route('pengawas.ubahpassword') }}"
                        method="POST"
                >
                @csrf
                    <div class="mb-2">
                        <label for="passl" class="form-label">Pasword Lama</label>
                        <input type="password" class="form-control" id="passl" name="passl" autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label for="passb" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="passb" name="passb" autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label for="passu" class="form-label">Ulangi Password Baru</label>
                        <input type="password" class="form-control" id="passu" name="passu" autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-warning btnup">Ubah Password</button>
                </form>
            </div>
          </div>
          <!--/ Activity Timeline -->
         
        
        </div>
      </div>
      <!--/ User Profile Content -->
    </div>
    <!-- / Content -->

    

    <div class="content-backdrop fade"></div>
  </div>
@endsection
