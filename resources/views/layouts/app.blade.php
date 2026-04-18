<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Terminliste for hundeprøver' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
</head>
<body>
<header>
    <div class="container">
        <h1>Terminliste for hundeprøver</h1>
        <p>Laravel og MariaDB for administrasjon av hundeprøver.</p>
        <nav>
            <a href="{{ route('home') }}">Terminliste</a>
            <a href="{{ route('admin') }}">Administrasjon</a>
        </nav>
    </div>
</header>

<main class="container">
    @if (session('flash'))
        <div class="notice {{ session('flash.type') }}">{{ session('flash.message') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
