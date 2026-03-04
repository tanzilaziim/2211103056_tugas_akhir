<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 text-surface-400 antialiased">
    @php
        $adminPageTitle = trim($__env->yieldContent('admin_page_title')) ?: 'Dashboard';
        $adminDatasetName = trim($__env->yieldContent('admin_dataset_name')) ?: '2025.zip';
    @endphp

    <div class="flex min-h-screen bg-surface-50" data-admin-shell>
        @include('components.admin.sidebar')

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            @include('components.admin.header', [
                'pageTitle' => $adminPageTitle,
                'datasetName' => $adminDatasetName,
            ])

            <main class="flex-1 p-4 md:p-6 lg:p-7">
                @yield('content')
                @include('components.admin.footer')
            </main>
        </div>
    </div>
</body>
</html>
