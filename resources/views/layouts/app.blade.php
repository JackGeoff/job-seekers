<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Modern Job Search Platform for Kenya">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <title>{{ config('app.name', 'JobSeekers') }}@if (isset($title)) - {{ $title }}@endif</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900">
    @include('components.navigation')

    <main>
        @if (session('success') || session('error'))
            <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                <div class="rounded-xl border p-4 text-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}" role="status">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('components.footer')
</body>
</html>
