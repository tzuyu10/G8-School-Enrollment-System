<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">
    
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
    ])
</head>
<body>
    @include('common.navbar')
    @include('common.sidebar')
    <div class="main-content p-4">
        <h1><b>Student Profile</b></h1>
        <p>Main page content goes here.</p>
    </div>
</body>
</html>