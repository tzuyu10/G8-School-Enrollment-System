<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/pup-logo.png') }}">
    <title>Page Not Found | PUP Enrollment Portal</title>
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
    ])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>

    <a href="{{ route('login') }}" class="brand-mark d-flex align-items-center gap-2">
        <div class="brand-seal">
            <img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="PUP seal" style="width:42px;height:42px;border-radius:50%;">
        </div>
        <div>
            <div class="brand-name">PUP Enrollment Portal</div>
            <div class="brand-sub">PUP School Enrollment System</div>
        </div>
    </a>

    <div class="hero-left" aria-hidden="true">
        <span class="eyebrow-badge">404 Error</span>
        <h1 class="hero-headline">
            PAGE<br>
            NOT<br>
            <span class="text-gold">FOUND</span>
        </h1>
    </div>

    <div class="hero-right">
        <div class="login-card">
            <p class="card-title-custom mb-1">This page does not exist</p>
            <p class="card-sub mb-4">The link may be outdated or the page may have moved.</p>
            <a href="{{ route('login') }}" class="btn btn-signin w-100">Back to Portal</a>
        </div>
    </div>
</section>
@include('common.footer')
</body>
</html>
