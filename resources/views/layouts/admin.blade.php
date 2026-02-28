<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 text-surface-400 antialiased">
    <div class="flex min-h-screen">
        @include('components.admin.sidebar')

        <div class="flex min-h-screen flex-1 flex-col">
            @include('components.admin.header')

            <main class="flex-1 p-6 md:p-8">
                @yield('content')
            </main>

            @include('components.admin.footer')
        </div>
    </div>
</body>
</html>
