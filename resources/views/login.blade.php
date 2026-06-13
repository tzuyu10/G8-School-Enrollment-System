<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/pup-logo.png') }}">
    <title>PUP Enrollment Portal</title>
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

<!-- HERO -->
<section class="hero">

    <div class="hero-bg"></div>
    <div class="hero-veil"></div>

    <!-- Brand -->
    <a href="#" class="brand-mark d-flex align-items-center gap-2">
        <div class="brand-seal">
            <img src="{{ asset('build/assets/images/pup-logo.png') }}" alt="PUP seal" style="width:42px;height:42px;border-radius:50%;">
        </div>
        <div>
            <div class="brand-name">PUP Enrollment Portal </div>
            <div class="brand-sub">PUP School Enrollment System</div>
        </div>
    </a>

    <!-- Left tagline -->
    <div class="hero-left" aria-hidden="true">
        <span class="eyebrow-badge">Reforging Excellence</span>
        <h1 class="hero-headline">
            ENROLL<br>
            NOW IN<br>
            <span class="text-gold">SINTA</span>
        </h1>
    </div>

    <!-- Login card -->
    <div class="hero-right">
        <div class="login-card">

            <p class="card-title-custom mb-1">Welcome back, Iskolar!</p>
            <p class="card-sub mb-4">Login and stay connected.</p>

            <form action="#" method="POST">

                <!-- Student ID -->
                <div class="mb-3">
                    <label class="form-label" for="student_id">Student ID Number</label>
                    <input 
                        id="student_id"
                        type="text"
                        class="form-control"
                        placeholder="202X-XXXX-MN-0"
                        autocomplete="off"
                    >
                </div>

                <!-- Password -->
                <div class="mb-1">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input
                            id="password"
                            type="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                        >
                        <button
                            type="button"
                            class="btn pw-toggle px-3"
                            id="pw-toggle-btn"
                            aria-label="Toggle password visibility"
                        >
                            <i class="bi bi-eye" id="pw-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 mt-1">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-signin w-100">Sign in to Portal</button>
            </form>

            <p class="contact-note text-center mt-3 mb-0">
                Don't have an account? <a href="#">Register</a>
            </p>

        </div>
    </div>

</section>

<!-- FOOTER -->
    @include('common.footer')

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const pwInput  = document.getElementById('password');
    const pwIcon   = document.getElementById('pw-icon');
    const pwToggle = document.getElementById('pw-toggle-btn');

    pwToggle.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type   = isHidden ? 'text' : 'password';
        pwIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>

</body>
</html>