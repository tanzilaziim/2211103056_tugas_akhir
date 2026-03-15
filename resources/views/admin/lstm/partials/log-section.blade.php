<section data-lstm-section="log" class="hidden space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative">
                <button type="button" data-date-toggle="logrun" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-label="logrun">01 Januari 2026</span>
                </button>
                <div data-date-menu="logrun" class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single" data-date-scope="logrun">
                        <p class="mb-2 text-xs font-medium text-surface-300">Filter tanggal run</p>
                        <input data-date-single-input="logrun" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <button type="button" data-date-single-apply="logrun" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                            <button type="button" data-date-single-all="logrun" class="w-full rounded-lg border border-primary-300 bg-primary-50 px-3 py-2 text-sm font-medium text-primary-300">Semua Data</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button type="button" data-log-run-toggle class="inline-flex min-w-[210px] items-center justify-between gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <span data-log-run-label>30-12-2025 22.59</span>
                    <i class="ph ph-caret-down text-base text-primary-300"></i>
                </button>
                <div data-log-run-menu class="absolute right-0 top-full z-20 mt-2 hidden w-56 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg"></div>
            </div>
        </div>

        <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-4 shadow-sm sm:p-6">
            <h2 class="mb-6 text-xl font-bold text-surface-400 sm:text-2xl">Detail Proses Prediksi</h2>
            <div data-log-steps class="space-y-3"></div>
        </div>
    </section>
