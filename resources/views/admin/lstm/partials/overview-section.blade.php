<section data-lstm-section="overview" class="flex flex-col gap-6">
        <div class="rounded-[15px] border border-surface-200 bg-surface-100 shadow-sm">
            <div class="grid grid-cols-1 divide-y divide-surface-200 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                <div class="p-7">
                    <div class="flex flex-col gap-6">
                        <h2 class="text-2xl font-bold text-surface-400">Jalankan Prediksi</h2>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <button type="button" data-date-toggle="control" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                                    <span data-date-label="control">01 Januari 2026</span>
                                </button>
                                <div data-date-menu="control" class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                                    <div data-date-mode="single" data-date-scope="control">
                                        <div class="mb-2 flex items-center justify-between">
                                            <button type="button" data-date-prev="control" class="rounded-lg p-1 hover:bg-surface-200">
                                                <i class="ph ph-caret-left text-base text-surface-300"></i>
                                            </button>
                                            <span data-date-current="control" class="text-sm font-medium text-surface-300"></span>
                                            <button type="button" data-date-next="control" class="rounded-lg p-1 hover:bg-surface-200">
                                                <i class="ph ph-caret-right text-base text-surface-300"></i>
                                            </button>
                                        </div>
                                        <div data-date-list="control" class="max-h-48 space-y-1 overflow-y-auto"></div>
                                    </div>
                                    <div data-date-mode="range" data-date-scope="control" class="hidden space-y-2">
                                        <p class="text-xs font-medium text-surface-300">Pilih rentang 7 hari</p>
                                        <input data-date-range-start="control" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                        <input data-date-range-end="control" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                        <button type="button" data-date-range-apply="control" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                                    </div>
                                    <div data-date-mode="month" data-date-scope="control" class="hidden space-y-2">
                                        <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                                        <input data-date-month="control" type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                        <button type="button" data-date-month-apply="control" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="button" data-start-prediction class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-primary-300 px-4 py-2.5 text-base text-surface-50 transition-colors hover:bg-primary-400">
                                <i class="ph ph-rocket-launch text-xl"></i>
                                Mulai Prediksi
                            </button>
                            <button type="button" data-stop-prediction class="hidden items-center justify-center gap-2 rounded-full bg-danger-300 px-4 py-2.5 text-base text-surface-50 transition-colors hover:bg-danger-400">
                                <i class="ph ph-stop-circle text-xl"></i>
                                Stop
                            </button>
                        </div>

                        <p class="text-sm leading-5 text-ispu-sangat-tidak-sehat">
                            *Pilih bulan prediksi dengan benar sebelum menekan tombol.
                        </p>
                    </div>
                </div>

                <div class="p-7">
                    <div class="flex flex-col gap-6">
                        <h2 class="text-2xl font-bold text-surface-400">Status Operasi Prediksi</h2>

                        <div class="flex items-center gap-2 text-sm text-surface-300">
                            <span>Tanggal prediksi dijalankan</span>
                            <span>:</span>
                            <span data-status-date>01-01-2026 23.59 WIB</span>
                        </div>

                        <div class="flex justify-center">
                            <div data-status-pill class="w-[200px] rounded-full border border-surface-200 bg-primary-100 py-2.5 text-center text-base text-ispu-baik">
                                Sukses
                            </div>
                        </div>
                        <p data-status-step class="hidden text-center text-sm font-bold text-warning-300"></p>

                        <div class="flex flex-wrap gap-3">
                            <button type="button" data-go-log class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-surface-200">
                                Log Proses Prediksi
                                <i class="ph ph-arrow-right text-base text-primary-300"></i>
                            </button>
                            <button type="button" data-see-result class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-surface-200">
                                Hasil Run Prediksi
                                <i class="ph ph-arrow-right text-base text-primary-300"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[15px] border border-surface-200 bg-surface-100 shadow-sm">
            <div class="px-7 pb-5 pt-7">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-surface-400">Output Pipeline Prediksi</h2>
                        <p class="mt-1 text-sm text-surface-300">
                            Run aktif untuk ditampilkan: <span data-active-run-label class="font-semibold text-primary-300">#1 - 01-01-2025 23.59</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <button type="button" data-date-toggle="table" class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                                <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                                <span data-date-label="table">01 Januari 2026</span>
                            </button>
                            <div data-date-menu="table" class="absolute right-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                                <div data-date-mode="single" data-date-scope="table">
                                    <p class="mb-2 text-xs font-medium text-surface-300">Pilih tanggal run</p>
                                    <input data-date-single-input="table" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <button type="button" data-date-single-apply="table" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                                        <button type="button" data-date-single-all="table" class="w-full rounded-lg border border-primary-300 bg-primary-50 px-3 py-2 text-sm font-medium text-primary-300">Semua Data</button>
                                    </div>
                                </div>
                                <div data-date-mode="range" data-date-scope="table" class="hidden space-y-2">
                                    <p class="text-xs font-medium text-surface-300">Pilih rentang 7 hari</p>
                                    <input data-date-range-start="table" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                    <input data-date-range-end="table" type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                    <button type="button" data-date-range-apply="table" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                                </div>
                                <div data-date-mode="month" data-date-scope="table" class="hidden space-y-2">
                                    <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                                    <input data-date-month="table" type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                                    <button type="button" data-date-month-apply="table" class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-7 pb-7">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-primary-300">
                                <th class="rounded-tl-xl border-l-2 border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Waktu Eksekusi</th>
                                <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Tanggal Prediksi</th>
                                <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Status</th>
                                <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Hasil Prediksi</th>
                                <th class="rounded-tr-xl border-r-2 border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-lstm-table-body class="bg-primary-50"></tbody>
                    </table>
                </div>

                <div data-lstm-pagination class="mt-4 flex items-center justify-end gap-2"></div>
            </div>
        </div>
    </section>
