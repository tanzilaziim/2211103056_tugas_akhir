@extends('layouts.public')

@section('title', 'Prediksi')

@section('content')
<div data-page="prediction" class="min-h-screen bg-slate-50 font-[Inter,sans-serif]">
    <div class="mx-auto w-full max-w-[1192px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="mb-7 text-center">
            <h1 class="text-[28px] font-bold leading-tight text-slate-900 sm:text-[32px]">
                Prediksi Kualitas Udara Kabupaten Indramayu
            </h1>
            <p class="mt-3 text-base text-slate-600">
                Prediksi kualitas udara konsentrasi partikulat PM10 dan PM2.5 di Kabupaten Indramayu
            </p>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-3">
            <div class="relative inline-flex items-center rounded-full border border-primary-300 bg-white p-[3px]">
                <button data-period-btn="24 Jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm font-normal text-surface-50 transition-all">24 Jam</button>
                <button data-period-btn="7 Hari" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">7 Hari</button>
                <button data-period-btn="30 Hari" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">30 Hari</button>
            </div>

            <div class="relative">
                <button type="button" data-date-toggle class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-white px-4 py-1.5 text-sm text-slate-600 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-display>-</span>
                </button>
                <div data-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single">
                        <p class="mb-2 text-xs font-medium text-surface-300">Pilih tanggal (24 Jam)</p>
                        <input data-date-single type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-single-apply class="mt-2 w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="range" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih tanggal awal (otomatis 7 hari)</p>
                        <input data-date-range-start type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <p data-date-range-preview class="rounded-lg bg-primary-50 px-3 py-2 text-xs text-primary-300"></p>
                        <button type="button" data-date-range-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="month" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                        <input data-date-month type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-month-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                </div>
            </div>

            <p data-access-range class="ml-auto text-right text-xs font-medium text-primary-300 sm:text-sm">
                Rentang prediksi tersedia: -
            </p>
        </div>

        <div class="mb-5 flex flex-col gap-4 sm:flex-row">
            <div data-pm10-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background: linear-gradient(180deg, rgba(37, 99, 235, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">PM10</h2>
                    <div class="flex items-center gap-2">
                        <span data-pm10-icon class="inline-flex items-center justify-center"></span>
                        <span data-pm10-status class="text-lg text-slate-600">-</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex min-w-[120px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Rata-rata:</span>
                        <span data-pm10-average class="ml-auto text-base font-normal">0</span>
                    </div>
                    <div class="flex min-w-[110px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Tertinggi:</span>
                        <span data-pm10-highest class="ml-auto text-base font-normal">0</span>
                    </div>
                    <div class="flex min-w-[110px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Terendah:</span>
                        <span data-pm10-lowest class="ml-auto text-base font-normal">0</span>
                    </div>
                </div>
            </div>

            <div data-pm25-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background: linear-gradient(180deg, rgba(22, 163, 74, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">PM2.5</h2>
                    <div class="flex items-center gap-2">
                        <span data-pm25-icon class="inline-flex items-center justify-center"></span>
                        <span data-pm25-status class="text-lg text-slate-600">-</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex min-w-[120px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Rata-rata:</span>
                        <span data-pm25-average class="ml-auto text-base font-normal">0</span>
                    </div>
                    <div class="flex min-w-[110px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Tertinggi:</span>
                        <span data-pm25-highest class="ml-auto text-base font-normal">0</span>
                    </div>
                    <div class="flex min-w-[110px] flex-1 items-center gap-2 rounded-[10px] border border-slate-200 px-2 py-1.5">
                        <span class="whitespace-nowrap text-base text-slate-600">Terendah:</span>
                        <span data-pm25-lowest class="ml-auto text-base font-normal">0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto]">
            <div class="min-w-0">
                <div class="mb-4 rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 data-chart-title="pm10" class="mb-4 text-xl font-bold text-slate-900">PM10 - Grafik Prediksi (24 Jam)</h3>
                    <div class="h-[280px] w-full"><canvas id="pm10-chart"></canvas></div>
                </div>
                <div class="rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 data-chart-title="pm25" class="mb-4 text-xl font-bold text-slate-900">PM2.5 - Grafik Prediksi (24 Jam)</h3>
                    <div class="h-[280px] w-full"><canvas id="pm25-chart"></canvas></div>
                </div>
            </div>

            <div class="w-full shrink-0 lg:w-[370px]">
                <div class="h-full rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-xl font-bold text-slate-900">Detail Prediksi</h3>

                    <div class="relative mb-4 inline-flex items-center rounded-full border border-primary-300 bg-white p-[3px]">
                        <button data-tab="pm10" class="relative z-10 rounded-full bg-primary-300 px-4 py-1.5 text-sm font-normal text-surface-50 transition-all">PM10</button>
                        <button data-tab="pm25" class="relative z-10 rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">PM2.5</button>
                    </div>

                    <div class="max-h-[560px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-t-2 border-primary-300">
                                    <th data-detail-time-head class="py-2 text-center font-normal text-slate-600">Jam</th>
                                    <th class="py-2 text-center font-normal text-slate-600">Konsentrasi</th>
                                    <th class="py-2 text-center font-normal text-slate-600">Indikator Kesehatan</th>
                                </tr>
                            </thead>
                            <tbody data-detail-table-body></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
