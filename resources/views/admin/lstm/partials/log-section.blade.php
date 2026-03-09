<section data-lstm-section="log" class="hidden space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center rounded-full border border-primary-300 bg-surface-100 p-[3px]">
                <button data-log-range="24jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm text-surface-50">24 Jam</button>
                <button data-log-range="7hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">7 Hari</button>
                <button data-log-range="30hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">30 Hari</button>
            </div>

            <div class="relative">
                <button type="button" data-date-toggle="log" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-label="log">01 Januari 2026</span>
                </button>
                <div data-date-menu="log" class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single" data-date-scope="log">
                        <div class="mb-2 flex items-center justify-between">
                            <button type="button" data-date-prev="log" class="rounded-lg p-1 hover:bg-surface-200">
                                <i class="ph ph-caret-left text-base text-surface-300"></i>
                            </button>
                            <span data-date-current="log" class="text-sm font-medium text-surface-300"></span>
                            <button type="button" data-date-next="log" class="rounded-lg p-1 hover:bg-surface-200">
                                <i class="ph ph-caret-right text-base text-surface-300"></i>
                            </button>
                        </div>
                        <div data-date-list="log" class="max-h-48 space-y-1 overflow-y-auto"></div>
                    </div>
                    <div data-date-mode="range" data-date-scope="log" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih rentang 7 hari</p>
                        <input data-date-range-start="log" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <input data-date-range-end="log" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-range-apply="log" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="month" data-date-scope="log" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                        <input data-date-month="log" type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-month-apply="log" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button type="button" data-log-run-toggle class="inline-flex min-w-[210px] items-center justify-between gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                    <span data-log-run-label>30-12-2025 22.59</span>
                    <i class="ph ph-caret-down text-base text-primary-300"></i>
                </button>
                <div data-log-run-menu class="absolute right-0 top-full z-20 mt-2 hidden w-56 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg">
                    <button type="button" data-log-run-option="run-1" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">30-12-2025 22.59</button>
                    <button type="button" data-log-run-option="run-2" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">31-12-2025 08.15</button>
                    <button type="button" data-log-run-option="run-3" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">01-01-2026 06.45</button>
                </div>
            </div>
        </div>

        <div class="rounded-[15px] border border-surface-200 bg-surface-100 p-4 shadow-sm sm:p-6">
            <h2 class="mb-6 text-xl font-bold text-surface-400 sm:text-2xl">Detail Proses Prediksi</h2>
            <div data-log-steps class="space-y-3"></div>
        </div>
    </section>
