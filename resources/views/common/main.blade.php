<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PUP Enrollment Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pup-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @include('common.navbar')
    @include('common.sidebar')

    <main class="main-content p-4" id="mainContent" style="margin-left: 180px; margin-top: 60px; transition: margin-left 0.25s ease;">
        @yield('content')
    </main>

    <script src="{{ asset('js/sidebar.js') }}"></script>
    @stack('scripts')
</body>
</html>
