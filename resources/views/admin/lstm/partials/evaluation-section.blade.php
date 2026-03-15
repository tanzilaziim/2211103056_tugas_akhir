<section data-lstm-section="evaluation" class="hidden space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
                <button type="button" data-date-toggle="evalrun" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-label="evalrun">01 Januari 2026</span>
                </button>
                <div data-date-menu="evalrun" class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single" data-date-scope="evalrun">
                        <p class="mb-2 text-xs font-medium text-surface-300">Filter tanggal run</p>
                        <input data-date-single-input="evalrun" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <button type="button" data-date-single-apply="evalrun" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                            <button type="button" data-date-single-all="evalrun" class="w-full rounded-lg border border-primary-300 bg-primary-50 px-3 py-2 text-sm font-medium text-primary-300">Semua Data</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button type="button" data-eval-run-toggle class="inline-flex min-w-[210px] items-center justify-between gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <span data-eval-run-label>30-12-2025 22.59</span>
                    <i class="ph ph-caret-down text-base text-primary-300"></i>
                </button>
                <div data-eval-run-menu class="absolute right-0 top-full z-20 mt-2 hidden w-56 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg"></div>
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
                <div class="inline-flex items-center rounded-full border border-primary-300 bg-surface-100 p-[3px]">
                    <button data-eval-range="24jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm text-surface-50">24 Jam</button>
                    <button data-eval-range="7hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">7 Hari</button>
                    <button data-eval-range="30hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">30 Hari</button>
                </div>
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
            <p data-eval-window-info class="mb-4 text-sm text-surface-300"></p>
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
