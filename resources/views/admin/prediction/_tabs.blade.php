<div class="mb-6 inline-flex rounded-full border border-primary-300 bg-surface-100 p-[3px]">
    <a
        href="{{ route('admin.prediction.data') }}"
        class="rounded-full px-8 py-1.5 text-sm font-normal transition-colors duration-200 {{ request()->routeIs('admin.prediction.data') ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:text-primary-300' }}"
    >
        Data Hasil
    </a>
    <a
        href="{{ route('admin.prediction.chart') }}"
        class="rounded-full px-8 py-1.5 text-sm font-normal transition-colors duration-200 {{ request()->routeIs('admin.prediction.chart') ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:text-primary-300' }}"
    >
        Grafik Prediksi
    </a>
</div>
