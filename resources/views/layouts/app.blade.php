<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Modern Job Search Platform for Kenya">

    <title>{{ config('app.name', 'JobSeekers') }}@if (isset($title)) - {{ $title }}@endif</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900">
    @include('components.navigation')

    <main>
        @yield('content')
    </main>

    @include('components.footer')
</body>
</html>
