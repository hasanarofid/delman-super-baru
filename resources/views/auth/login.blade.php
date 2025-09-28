@extends('layouts.front')
@section('title', 'Login')
@section('css')
<link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
@endsection
@section('content')

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="{{ asset('delmansupernew.png') }}" height="150px" alt="DelmanSuper Logo" class="card-img">
                            <h4 class="mb-1 pt-3 text-center">Admin DelmanSuper Login</h4>
                            <p class="text-muted mb-0">Model manajemen supervisi Kolegial Pengawas Sekolah Provinsi Banten</p>
                            <div class="badge bg-primary mt-2">
                              <i class="ti ti-shield-check me-1"></i>
                              ADMIN ACCESS
                            </div>
                        </div>
                        <hr>
                        <form role="form" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti ti-mail text-primary"></i>
                                    </span>
                                    <input type="email" id="email"
                                        class="form-control form-control-lg border-start-0 @error('email') is-invalid @enderror"
                                        placeholder="Masukkan email Anda" aria-label="Email" name="email" value="{{ old('email') }}"
                                        required autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-dark">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti ti-lock text-primary"></i>
                                    </span>
                                    <input id="password" type="password" placeholder="Masukkan password Anda"
                                        class="form-control form-control-lg border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                                        <i class="ti ti-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
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
                                <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-3">
                                    <i class="ti ti-login me-2"></i>{{ __('Login') }}
                                </button>
                            </div>
                        </form>
                        
                        <!-- Footer dengan informasi tambahan -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="ti ti-shield-check me-1"></i>
                                Sistem keamanan terjamin dengan enkripsi SSL
                            </small>
                        </div>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    
    if (togglePassword && passwordInput && togglePasswordIcon) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordIcon.classList.remove('ti-eye');
                togglePasswordIcon.classList.add('ti-eye-off');
            } else {
                passwordInput.type = 'password';
                togglePasswordIcon.classList.remove('ti-eye-off');
                togglePasswordIcon.classList.add('ti-eye');
            }
        });
    }
});
</script>
@endsection
