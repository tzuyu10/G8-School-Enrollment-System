<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired | PUP Enrollment Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>
    <div class="hero-right">
        <div class="login-card">
            <p class="card-title-custom mb-1">Session expired</p>
            <p class="card-sub mb-4">Please refresh your session and try again.</p>
            <a href="{{ route('login') }}" class="btn btn-signin w-100">Back to Login</a>
        </div>
    </div>
</section>
</body>
</html>
