<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Capstone')</title>
    @if (! app()->environment('testing'))
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @endif
</head>
<body class="app-shell">
<header class="app-shell__header py-3 mb-4">
    <div class="container-xl d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <a href="{{ route('jobs.index') }}" class="app-shell__brand fs-4">Capstone Hiring</a>
            <div class="app-shell__tagline">Lesson 11 · Production-style app with Pennant</div>
        </div>
        <nav class="d-flex gap-3">
            <a href="{{ route('jobs.index') }}" class="app-shell__nav-link">Jobs</a>
            <a href="{{ route('capstone.dashboard') }}" class="app-shell__nav-link">Feature Flags</a>
        </nav>
    </div>
</header>

<main class="container-xl pb-5">
    @if (session('status'))
        <div class="notice notice--success">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
