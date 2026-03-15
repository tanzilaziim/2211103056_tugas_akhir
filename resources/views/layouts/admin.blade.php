<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden bg-surface-50 text-surface-400 antialiased">
    @php
        $adminPageTitle = trim($__env->yieldContent('admin_page_title')) ?: 'Dashboard';
        $latestImportedDataset = \App\Models\DataImport::query()
            ->whereIn('status', ['processed', 'success'])
            ->latest('id')
            ->value('original_name');
        $adminDatasetName = trim($__env->yieldContent('admin_dataset_name')) ?: ($latestImportedDataset ?: 'Belum ada dataset');
    @endphp

    <div class="relative flex h-screen overflow-hidden bg-surface-50" data-admin-shell>
        @include('components.admin.sidebar')

        <button
            type="button"
            class="absolute left-[327px] top-20 z-50 hidden -translate-x-1/2 items-center justify-center rounded-full border border-surface-200 bg-surface-100 p-1.5 text-surface-300 shadow-sm transition-[left,transform] hover:bg-surface-50 lg:flex"
            data-admin-sidebar-desktop-toggle
            aria-label="Collapse sidebar"
        >
            <i class="ph ph-caret-left text-lg" data-admin-sidebar-desktop-icon></i>
        </button>

        <div class="flex h-screen min-w-0 flex-1 flex-col overflow-hidden">
            @include('components.admin.header', [
                'pageTitle' => $adminPageTitle,
                'datasetName' => $adminDatasetName,
            ])

            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-7">
                @yield('content')
                @include('components.admin.footer')
            </main>
        </div>
    </div>
</body>
</html>
