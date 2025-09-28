@extends('layouts.pengawas.main')
@section('title','Login')
@section('content')

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-4">
        <!-- Login -->
        <div class="card">
          <div class="card-body">
            <img src="{{ asset('delmansupernew.png') }}" height="150px" alt="Image placeholder" class="card-img">
            <h5 class="mb-1 pt-3 text-center">Sistem DelmanSuper</h5>
              <hr>
            <form role="form" method="POST" action="{{ route('superPengawasLogin') }}">
                @csrf
                <div class="mb-3">
                  <!-- Mengubah name dari 'email' menjadi 'identifier' -->
                  <input type="text" class="form-control form-control-lg @error('identifier') is-invalid @enderror" 
                         placeholder="Email atau NIP" name="identifier" 
                         value="{{ old('identifier') }}" required autofocus>
                  @error('identifier')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <input id="password" type="password" placeholder="Password" 
                         class="form-control form-control-lg @error('password') is-invalid @enderror" 
                         name="password" required>
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                         {{ old('remember') ? 'checked' : '' }}>
                  <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                  </label>
                </div>
                
                <div class="text-center">
                  <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">
                    {{ __('Login') }}
                  </button>
                </div>
            </form>
          </div>
        </div>
        <!-- /Register -->
      </div>
    </div>
  </div>
@endsection
