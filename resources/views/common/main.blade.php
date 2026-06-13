<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
    ])
</head>
<body>

        <div class="my-3">
            @yield('content')
        </div>

        @include('common.footer')
</body>
</html>