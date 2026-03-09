<section data-lstm-section="evaluation" class="hidden space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center rounded-full border border-primary-300 bg-surface-100 p-[3px]">
                <button data-eval-range="24jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm text-surface-50">24 Jam</button>
                <button data-eval-range="7hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">7 Hari</button>
                <button data-eval-range="30hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">30 Hari</button>
            </div>

            <div class="relative">
                <button type="button" data-date-toggle="evaluation" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-label="evaluation">01 Januari 2026</span>
                </button>
                <div data-date-menu="evaluation" class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single" data-date-scope="evaluation">
                        <div class="mb-2 flex items-center justify-between">
                            <button type="button" data-date-prev="evaluation" class="rounded-lg p-1 hover:bg-surface-200">
                                <i class="ph ph-caret-left text-base text-surface-300"></i>
                            </button>
                            <span data-date-current="evaluation" class="text-sm font-medium text-surface-300"></span>
                            <button type="button" data-date-next="evaluation" class="rounded-lg p-1 hover:bg-surface-200">
                                <i class="ph ph-caret-right text-base text-surface-300"></i>
                            </button>
                        </div>
                        <div data-date-list="evaluation" class="max-h-48 space-y-1 overflow-y-auto"></div>
                    </div>
                    <div data-date-mode="range" data-date-scope="evaluation" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih rentang 7 hari</p>
                        <input data-date-range-start="evaluation" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <input data-date-range-end="evaluation" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-range-apply="evaluation" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="month" data-date-scope="evaluation" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                        <input data-date-month="evaluation" type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-month-apply="evaluation" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button type="button" data-eval-run-toggle class="inline-flex min-w-[210px] items-center justify-between gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <span data-eval-run-label>30-12-2025 22.59</span>
                    <i class="ph ph-caret-down text-base text-primary-300"></i>
                </button>
                <div data-eval-run-menu class="absolute right-0 top-full z-20 mt-2 hidden w-56 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg">
                    <button type="button" data-eval-run-option="run-1" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">30-12-2025 22.59</button>
                    <button type="button" data-eval-run-option="run-2" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">31-12-2025 08.15</button>
                    <button type="button" data-eval-run-option="run-3" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">01-01-2026 06.45</button>
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-4 text-2xl font-bold text-surface-400">Matriks Evaluasi</h2>
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-6 shadow-sm">
                    <h3 class="mb-4 text-2xl font-bold text-surface-400">PM10</h3>
                    <div class="grid grid-cols-2 gap-2 lg:grid-cols-4">
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">MAE:</span><span data-eval-pm10-mae class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">MSE:</span><span data-eval-pm10-mse class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">RMSE:</span><span data-eval-pm10-rmse class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">R2:</span><span data-eval-pm10-r2 class="ml-auto text-lg text-primary-300">0,0000</span></div>
                    </div>
                </div>

                <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-6 shadow-sm">
                    <h3 class="mb-4 text-2xl font-bold text-surface-400">PM2.5</h3>
                    <div class="grid grid-cols-2 gap-2 lg:grid-cols-4">
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">MAE:</span><span data-eval-pm25-mae class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">MSE:</span><span data-eval-pm25-mse class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">RMSE:</span><span data-eval-pm25-rmse class="ml-auto text-lg text-primary-300">0,0000</span></div>
                        <div class="flex items-center gap-1 rounded-xl border border-surface-200 px-2 py-2"><span class="text-lg text-surface-300">R2:</span><span data-eval-pm25-r2 class="ml-auto text-lg text-primary-300">0,0000</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-bold text-surface-400">Aktual vs Prediksi</h2>
                <div class="flex flex-wrap items-center gap-4 text-xs text-surface-300">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-block w-10 border-t-2 border-dashed border-primary-300"></span>
                        <span> = Aktual</span>
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-block w-10 border-t-2 border-primary-300"></span>
                        <span> = Prediksi</span>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-6 shadow-sm">
                    <h3 class="mb-4 text-2xl font-bold text-surface-400">PM10</h3>
                    <div class="h-[320px] w-full"><canvas id="lstm-eval-pm10-chart"></canvas></div>
                </div>
                <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-6 shadow-sm">
                    <h3 class="mb-4 text-2xl font-bold text-surface-400">PM2.5</h3>
                    <div class="h-[320px] w-full"><canvas id="lstm-eval-pm25-chart"></canvas></div>
                </div>
            </div>
        </div>
    </section>
