@php
    $title = $pageTitle ?? 'Dashboard';
    $dataset = $datasetName ?? '2025.zip';
    $today = \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y');
@endphp

<header class="h-16 shrink-0 border-b border-surface-200 bg-surface-100">
    <div class="flex h-full items-center justify-between gap-4 px-4 md:px-6 lg:px-7">
        <div class="flex min-w-0 items-center gap-2 md:gap-3">
            <button
                type="button"
                class="rounded-lg p-2 text-surface-300 transition-colors hover:bg-surface-200 lg:hidden"
                data-admin-sidebar-open
                aria-label="Buka menu"
            >
                <i class="ph ph-list text-2xl"></i>
            </button>

            <div class="min-w-0 truncate text-base text-surface-300 md:text-lg lg:text-xl">
                <span class="font-normal">Admin</span>
                <span class="font-normal"> / </span>
                <span class="font-bold text-surface-400">{{ $title }}</span>
            </div>
        </div>

        <div class="hidden items-center gap-4 lg:flex">
            <div class="flex items-center gap-2 rounded-full border border-surface-200 bg-surface-100 px-4 py-1.5">
                <span class="text-sm text-surface-300">Dataset:</span>
                <i class="ph ph-file text-base text-surface-300"></i>
                <span class="text-sm text-surface-300">{{ $dataset }}</span>
            </div>

            <div class="h-8 w-0.5 rounded-full bg-surface-200"></div>

            <span class="text-sm font-medium text-primary-300">{{ $today }}</span>
        </div>
    </div>
</header>
