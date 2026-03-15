<div data-page="admin-prediction-chart" class="min-h-full bg-surface-50">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-surface-400">Hasil Prediksi</h1>
        <p class="mt-1 text-base text-surface-300">Ringkasan grafik hasil prediksi</p>
    </div>

    @include('admin.prediction._tabs')

    <h2 class="mb-4 text-2xl font-bold text-surface-400">Grafik Prediksi</h2>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div class="inline-flex rounded-full border border-primary-300 bg-surface-100 p-[3px]">
            <button type="button" data-chart-range="24 Jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm text-surface-50">24 Jam</button>
            <button type="button" data-chart-range="7 Hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">7 Hari</button>
            <button type="button" data-chart-range="30 Hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">30 Hari</button>
        </div>

        <div class="relative">
            <button type="button" data-chart-date-toggle class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                <span data-chart-date-label>13 Februari 2025</span>
            </button>
            <div data-chart-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                <div data-chart-date-mode="single">
                    <div class="mb-2 flex items-center justify-between">
                        <button type="button" data-chart-date-prev class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                            <i class="ph ph-caret-left text-base text-surface-300"></i>
                        </button>
                        <span data-chart-date-current class="text-sm font-medium text-surface-300"></span>
                        <button type="button" data-chart-date-next class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                            <i class="ph ph-caret-right text-base text-surface-300"></i>
                        </button>
                    </div>
                    <div data-chart-date-list class="max-h-48 space-y-1 overflow-y-auto"></div>
                </div>
                <div data-chart-date-mode="range" class="hidden space-y-2">
                    <p class="text-xs font-medium text-surface-300">Pilih tanggal awal (otomatis 7 hari)</p>
                    <input data-chart-range-start type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                    <p data-chart-range-preview class="rounded-lg bg-primary-50 px-3 py-2 text-xs text-primary-300"></p>
                    <button type="button" data-chart-range-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                </div>
                <div data-chart-date-mode="month" class="hidden space-y-2">
                    <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                    <input data-chart-month type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                    <button type="button" data-chart-month-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                </div>
            </div>
        </div>

    </div>

    <div class="mb-6 grid grid-cols-1 gap-5 xl:grid-cols-2">
        <div data-chart-card-pm10 class="rounded-[15px] border border-surface-200 shadow-sm" style="background: linear-gradient(180deg, rgba(37, 99, 235, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM10</span>
                    <div class="flex items-center gap-2">
                        <span data-chart-pm10-icon class="inline-flex h-6 w-6 items-center justify-center text-surface-300"></span>
                        <span data-chart-pm10-status class="text-lg text-surface-300">Sedang</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span data-chart-pm10-avg class="text-sm text-ispu-sedang">0</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span data-chart-pm10-max class="text-sm text-ispu-sedang">0</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span data-chart-pm10-min class="text-sm text-ispu-sedang">0</span></div>
                </div>
            </div>
        </div>

        <div data-chart-card-pm25 class="rounded-[15px] border border-surface-200 shadow-sm" style="background: linear-gradient(180deg, rgba(22, 163, 74, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM2.5</span>
                    <div class="flex items-center gap-2">
                        <span data-chart-pm25-icon class="inline-flex h-6 w-6 items-center justify-center text-surface-300"></span>
                        <span data-chart-pm25-status class="text-lg text-surface-300">Baik</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span data-chart-pm25-avg class="text-sm text-ispu-baik">0</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span data-chart-pm25-max class="text-sm text-ispu-baik">0</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span data-chart-pm25-min class="text-sm text-ispu-baik">0</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div data-chart-wrap-pm10 class="rounded-[15px] border border-surface-200 bg-surface-100 p-5 shadow-sm">
            <h3 class="mb-3 text-xl font-bold text-surface-400">PM10 - Grafik Prediksi</h3>
            <div class="h-[280px] w-full">
                <canvas id="admin-prediction-pm10-chart"></canvas>
            </div>
        </div>
        <div data-chart-wrap-pm25 class="rounded-[15px] border border-surface-200 bg-surface-100 p-5 shadow-sm">
            <h3 class="mb-3 text-xl font-bold text-surface-400">PM2.5 - Grafik Prediksi</h3>
            <div class="h-[280px] w-full">
                <canvas id="admin-prediction-pm25-chart"></canvas>
            </div>
        </div>
    </div>
</div>
