<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/pup-logo.png') }}">
    <title>PUP Enrollment Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>

    <a href="#" class="brand-mark d-flex align-items-center gap-2">
        <div class="brand-seal">
            <img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="PUP seal" style="width:42px;height:42px;border-radius:50%;">
        </div>
        <div>
            <div class="brand-name">PUP Enrollment Portal </div>
            <div class="brand-sub">PUP School Enrollment System</div>
        </div>
    </a>

    <div class="hero-left" aria-hidden="true">
        <span class="eyebrow-badge">Reforging Excellence</span>
        <h1 class="hero-headline">
            ENROLL<br>
            NOW IN<br>
            <span class="text-gold">SINTA</span>
        </h1>
    </div>

    <div class="hero-right">
        <div class="login-card">

            <p class="card-title-custom mb-1">Welcome back, Iskolar!</p>
            <p class="card-sub mb-4">Login and stay connected.</p>            
            @if (session('status'))
                <div class="alert alert-success py-2 small">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input 
                        id="email"
                        name="email"
                        type="email"
                        class="form-control"
                        placeholder="name@example.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >
                </div>

                <!-- Password -->
                <div class="mb-1">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-signin w-100">Sign in to Portal</button>
            </form>

            <p class="contact-note text-center mt-3 mb-0">
                Don't have an account? <a href="{{ url('/register') }}">Register</a>
            </p>

        </div>
    </div>

</section>

    @include('common.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
