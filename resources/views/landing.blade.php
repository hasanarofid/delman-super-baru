@extends('layouts.front')
@section('title', 'Beranda - Digitalisasi Pengawasan Sekolah')

@section('css')
<style>
    /* Custom Styling untuk Landing Page DelmanSuper */
    :root {
        --primary-color: #7367f0;
        --primary-gradient: linear-gradient(135deg, #7367f0 0%, #9e95f5 100%);
        --dark-gradient: linear-gradient(135deg, #1e1e2d 0%, #2d2b42 100%);
        --accent-green: #28c76f;
        --accent-blue: #00cfdd;
    }

    body {
        font-family: 'Public Sans', sans-serif;
        background-color: #f8f7fa;
        color: #5d596c;
        overflow-x: hidden;
    }

    /* Navbar */
    .navbar-landing {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 25px 0 rgba(165, 163, 174, 0.15);
        transition: all 0.3s ease;
    }

    .navbar-brand img {
        height: 48px;
        width: auto;
        object-fit: contain;
    }

    /* Hero Section */
    .hero-section {
        background: var(--dark-gradient);
        position: relative;
        padding: 120px 0 100px;
        color: #ffffff;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(115, 103, 240, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .hero-section .container {
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        background: rgba(115, 103, 240, 0.2);
        color: #7367f0;
        border: 1px solid rgba(115, 103, 240, 0.4);
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .hero-title {
        font-size: 2.8rem;
        font-weight: 700;
        line-height: 1.25;
        color: #ffffff;
        margin-top: 1.5rem;
    }

    .hero-title span {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1.15rem;
        color: #b6b4d0;
        line-height: 1.7;
        margin-top: 1rem;
    }

    .btn-hero-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        padding: 12px 28px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 18px rgba(115, 103, 240, 0.4);
        transition: all 0.3s ease;
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(115, 103, 240, 0.6);
        color: #fff;
    }

    .btn-hero-outline {
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 12px 28px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-hero-outline:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* Hero Preview Card */
    .hero-preview-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    /* Feature Cards */
    .feature-card {
        background: #ffffff;
        border: 1px solid #ebd7ff33;
        border-radius: 12px;
        padding: 32px 24px;
        box-shadow: 0 4px 20px rgba(165, 163, 174, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(115, 103, 240, 0.15);
    }

    .feature-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    /* Role Access Portal Cards */
    .role-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eaeaec;
        box-shadow: 0 6px 20px rgba(165, 163, 174, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .role-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 35px rgba(165, 163, 174, 0.2);
    }

    .role-card-header {
        padding: 24px;
        color: #fff;
    }

    .role-card-body {
        padding: 24px;
    }

    /* Footer */
    .footer-landing {
        background: #1e1e2d;
        color: #a1a0b5;
        padding: 60px 0 30px;
    }
</style>
@endsection

@section('content')

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-landing fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="{{ asset('delmansupernew.png') }}" alt="DelmanSuper Logo">
            <div class="d-none d-sm-block ms-2">
                <span class="fw-bold text-dark fs-5 d-block leading-tight">Delman Super</span>
                <small class="text-muted fs-7">Supervisi Kolegial Pengawas</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" href="#beranda">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" href="#fitur">Fitur Unggulan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" href="#portal">Portal Login</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                <a href="{{ route('pengawas.login') }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    <i class="ti ti-user-check me-1"></i> Pengawas
                </a>
                <a href="{{ route('stakeholder.login') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                    <i class="ti ti-building-community me-1"></i> Stakeholder
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="beranda" class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="ti ti-award"></i> Model Supervisi Kolegial Pengawas Sekolah
                </div>
                <h1 class="hero-title">
                    Digitalisasi Pengawasan & <span>Evaluasi Pembelajaran</span>
                </h1>
                <p class="hero-subtitle">
                    Platform Sistem Informasi Delman Super hadir untuk meningkatkan mutu pendampingan sekolah binaan, otomatisasi formulir pengawasan, dan analisis data Monev BOSP secara akurat & real-time.
                </p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#portal" class="btn btn-hero-primary">
                        <i class="ti ti-login me-2"></i> Pilih Portal Login
                    </a>
                    <a href="#fitur" class="btn btn-hero-outline">
                        <i class="ti ti-info-circle me-2"></i> Pelajari Fitur
                    </a>
                </div>

                <!-- Stats Quick View -->
                <div class="row g-3 mt-5 pt-3 border-top border-secondary">
                    <div class="col-4">
                        <h4 class="text-white fw-bold mb-1"><i class="ti ti-school text-primary me-1"></i> Multi</h4>
                        <small class="text-muted">Sekolah Binaan</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-white fw-bold mb-1"><i class="ti ti-file-text text-success me-1"></i> Realtime</h4>
                        <small class="text-muted">Form Umpan Balik</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-white fw-bold mb-1"><i class="ti ti-brand-whatsapp text-info me-1"></i> Wablas</h4>
                        <small class="text-muted">Notifikasi Sistem</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-preview-card text-center">
                    <img src="{{ asset('delmansupernew.png') }}" class="img-fluid mb-4" style="max-height: 180px;" alt="Delman Super Logo">
                    <h5 class="text-white fw-bold mb-2">Platform Delman Super</h5>
                    <p class="text-muted fs-7 mb-4">Pengawasan Digital & Evaluasi Kualitas Pendidikan Provinsi Banten</p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('pengawas.login') }}" class="btn btn-success fw-semibold mb-2">
                            <i class="ti ti-user me-2"></i> Login Pengawas Sekolah
                        </a>
                        <a href="{{ route('stakeholder.login') }}" class="btn btn-info text-white fw-semibold">
                            <i class="ti ti-building-community me-2"></i> Login Stakeholder / Dinas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Unggulan -->
<section id="fitur" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-label-primary px-3 py-2 fs-7 mb-2">FITUR UTAMA</span>
            <h2 class="fw-bold text-dark">Solusi Terpadu Supervisi Pendidikan</h2>
            <p class="text-muted">Dirancang untuk memudahkan Pengawas, Dinas, dan Administrator dalam mengelola data pengawasan.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-light-primary text-primary">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Supervisi Kolegial</h5>
                    <p class="text-muted fs-7 mb-0">Pendampingan dan pembinaan sekolah binaan secara terstruktur dengan manajemen tugas pengawas yang efisien.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-light-success text-success">
                        <i class="ti ti-file-text"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Umpan Balik Dinamis</h5>
                    <p class="text-muted fs-7 mb-0">Pengisian kuesioner & bukti dokumen fisik secara digital lewat link umpan balik yang aman dan praktis.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-light-warning text-warning">
                        <i class="ti ti-chart-bar"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Dashboard Monev BOSP</h5>
                    <p class="text-muted fs-7 mb-0">Visualisasi analitik grafik Monev BOSP, Raport Pendidikan, dan rekapitulasi data sekolah interaktif.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-light-info text-info">
                        <i class="ti ti-brand-whatsapp"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Integrasi WA Blast</h5>
                    <p class="text-muted fs-7 mb-0">Layanan pengiriman pengumuman tugas & notifikasi otomatis via API Wablas terintegrasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Portal Login Section -->
<section id="portal" class="py-5" style="background-color: #f4f5fa;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-label-success px-3 py-2 fs-7 mb-2">PORTAL AKSES</span>
            <h2 class="fw-bold text-dark">Masuk ke Hak Akses Anda</h2>
            <p class="text-muted">Silakan pilih jenis akun yang sesuai untuk masuk ke panel kontrol Delman Super.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Pengawas Card -->
            <div class="col-md-6 col-lg-5">
                <div class="role-card">
                    <div class="role-card-header bg-success">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <i class="ti ti-user-check fs-1"></i>
                            <span class="badge bg-white text-success fw-bold">Pengawas</span>
                        </div>
                        <h4 class="text-white fw-bold mb-0">Portal Pengawas</h4>
                    </div>
                    <div class="role-card-body">
                        <p class="text-muted fs-7">Masuk untuk mengelola sekolah binaan, instrumen umpan balik, dan jadwal pendampingan pengawasan.</p>
                        <a href="{{ route('pengawas.login') }}" class="btn btn-success w-100 fw-semibold">
                            <i class="ti ti-login me-1"></i> Login Pengawas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stakeholder Card -->
            <div class="col-md-6 col-lg-5">
                <div class="role-card">
                    <div class="role-card-header bg-info">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <i class="ti ti-building-community fs-1"></i>
                            <span class="badge bg-white text-info fw-bold">Dinas / Stakeholder</span>
                        </div>
                        <h4 class="text-white fw-bold mb-0">Portal Stakeholder</h4>
                    </div>
                    <div class="role-card-body">
                        <p class="text-muted fs-7">Masuk untuk memantau ringkasan laporan Monev BOSP, grafik pencapaian, serta data eksekutif.</p>
                        <a href="{{ route('stakeholder.login') }}" class="btn btn-info text-white w-100 fw-semibold">
                            <i class="ti ti-login me-1"></i> Login Stakeholder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-landing">
    <div class="container">
        <div class="row gy-4 mb-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('delmansupernew.png') }}" height="40" alt="Delman Super Logo">
                    <h5 class="text-white fw-bold mb-0 ms-2">Delman Super</h5>
                </div>
                <p class="text-muted fs-7 leading-relaxed">
                    Model manajemen supervisi Kolegial Pengawas Sekolah Provinsi Banten. Sistem terpadu pengawasan digital, analisis Monev BOSP, dan pendampingan mutu pendidikan.
                </p>
            </div>
            <div class="col-lg-3 ms-auto">
                <h6 class="text-white fw-bold mb-3">Menu Cepat</h6>
                <ul class="list-unstyled text-muted fs-7">
                    <li class="mb-2"><a href="#beranda" class="text-muted text-decoration-none">Beranda</a></li>
                    <li class="mb-2"><a href="#fitur" class="text-muted text-decoration-none">Fitur Unggulan</a></li>
                    <li class="mb-2"><a href="{{ route('pengawas.login') }}" class="text-muted text-decoration-none">Login Pengawas</a></li>
                    <li class="mb-2"><a href="{{ route('stakeholder.login') }}" class="text-muted text-decoration-none">Login Stakeholder</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white fw-bold mb-3">Keamanan & Layanan</h6>
                <p class="text-muted fs-7">
                    <i class="ti ti-shield-check text-success me-1"></i> Terenkripsi SSL & Terintegrasi WhatsApp Broadcast API.
                </p>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-muted fs-7 pt-2">
            &copy; {{ date('Y') }} Delman Super Platform. All rights reserved.
        </div>
    </div>
</footer>

@endsection

@section('script')
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endsection
