@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'icon' => 'ph ph-house'],
        ['label' => 'Data Aktual', 'route' => 'admin.actual-data', 'active' => ['admin.actual-data*'], 'icon' => 'ph ph-folder-simple'],
        ['label' => 'LSTM', 'route' => null, 'active' => ['admin.lstm*'], 'icon' => 'ph ph-brain'],
        ['label' => 'Prediksi', 'route' => 'admin.prediction.data', 'active' => ['admin.prediction*'], 'icon' => 'ph ph-chart-bar'],
        ['label' => 'Pengaturan Akun', 'route' => 'admin.account', 'active' => ['admin.account*'], 'icon' => 'ph ph-gear-six'],
    ];
@endphp

<div class="fixed inset-0 z-30 hidden bg-surface-400/40 lg:hidden" data-admin-sidebar-overlay data-admin-sidebar-close></div>

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-[327px] -translate-x-full flex-col bg-primary-300 text-surface-50 transition-transform duration-300 ease-in-out lg:static lg:z-auto lg:h-screen lg:shrink-0 lg:translate-x-0 lg:transition-[width,transform]"
    data-admin-sidebar
>
    <div class="flex items-center gap-3 px-6 pb-6 pt-8">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-surface-50 text-primary-300">
            <i class="ph-fill ph-user text-[30px]"></i>
        </div>
        <span class="text-2xl font-semibold">Admin Panel</span>
    </div>

    <nav class="mt-2 flex flex-1 flex-col gap-2 px-[13px]">
        @foreach ($navItems as $item)
            @php
                $isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern));
                $isEnabled = filled($item['route']) && Route::has($item['route']);
                $href = $isEnabled ? route($item['route']) : '#';
            @endphp

            <a
                href="{{ $href }}"
                @if ($isEnabled) data-admin-sidebar-close @endif
                class="flex items-center gap-4 rounded-[10px] px-5 py-3 transition-colors duration-150
                    {{ $isActive ? 'bg-surface-50 text-primary-300' : 'bg-transparent text-surface-50 hover:bg-primary-400/35' }}
                    {{ $isEnabled ? '' : 'cursor-not-allowed opacity-70' }}"
                @if (! $isEnabled) aria-disabled="true" @endif
            >
                <i class="{{ $item['icon'] }} text-[28px] leading-none"></i>
                <span class="text-lg font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-[13px] pb-8">
        <div class="mb-6 border-t border-surface-50/30"></div>
        <a
            href="{{ route('admin.login') }}"
            data-admin-sidebar-close
            class="flex w-full items-center gap-4 rounded-[10px] bg-transparent px-5 py-3 text-surface-50 transition-colors duration-150 hover:bg-primary-400/35"
        >
            <i class="ph ph-sign-out text-[28px] leading-none"></i>
            <span class="text-lg font-medium">Keluar</span>
        </a>
    </div>
</aside>



