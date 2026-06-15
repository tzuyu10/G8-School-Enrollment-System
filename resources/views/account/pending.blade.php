<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending | PUP Enrollment Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>
    <a href="{{ route('login') }}" class="brand-mark d-flex align-items-center gap-2">
        <div class="brand-seal"><img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="PUP seal" style="width:42px;height:42px;border-radius:50%;"></div>
        <div><div class="brand-name">PUP Enrollment Portal</div><div class="brand-sub">PUP School Enrollment System</div></div>
    </a>
    <div class="hero-left" aria-hidden="true">
        <span class="eyebrow-badge">Account Review</span>
        <h1 class="hero-headline">PLEASE<br>WAIT<br><span class="text-gold">APPROVAL</span></h1>
    </div>
    <div class="hero-right">
        <div class="login-card">
            <p class="card-title-custom mb-1">Your account is pending</p>
            <p class="card-sub mb-4">Your registration is waiting for registrar or admin approval.</p>
            <a href="{{ route('login') }}" class="btn btn-signin w-100">Back to Login</a>
        </div>
    </div>
</section>
@include('common.footer')
</body>
</html>
