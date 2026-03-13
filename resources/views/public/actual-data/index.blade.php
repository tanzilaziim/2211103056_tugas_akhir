@extends('layouts.public')

@section('title', 'Data Aktual')

@section('content')
<div data-page="actual-data" data-api-url="{{ route('public.api.actual-data') }}" class="min-h-screen bg-slate-50 font-[Inter,sans-serif]">
    <main class="mx-auto w-full max-w-[1192px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">Data Kualitas Udara Kabupaten Indramayu</h1>
            <p class="mt-2 text-sm text-slate-500 sm:text-base">Data historis kualitas udara konsentrasi partikulat PM10 dan PM2.5 di Kabupaten Indramayu</p>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-3">
            <div class="relative inline-flex items-center rounded-full border border-primary-300 bg-white p-[3px]">
                <button data-range-btn="24 Jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm font-normal text-surface-50 transition-all">24 Jam</button>
                <button data-range-btn="7 Hari" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">7 Hari</button>
                <button data-range-btn="30 Hari" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">30 Hari</button>
            </div>

            <div class="relative">
                <button type="button" data-date-toggle class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-white px-4 py-1.5 text-sm text-slate-600 transition-colors hover:bg-primary-50">
                    <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                    <span data-date-display>13 Februari 2025</span>
                </button>
                <div data-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                    <div data-date-mode="single">
                        <p class="mb-2 text-xs font-medium text-surface-300">Pilih tanggal (24 Jam)</p>
                        <input data-date-single type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-single-apply class="mt-2 w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="range" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih tanggal awal</p>
                        <input data-date-range-start type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <p data-date-range-preview class="rounded-lg bg-primary-50 px-3 py-2 text-xs text-surface-300"></p>
                        <button type="button" data-date-range-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                    <div data-date-mode="month" class="hidden space-y-2">
                        <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                        <input data-date-month type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                        <button type="button" data-date-month-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row">
            <div data-pm10-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background-color: rgba(37,99,235,0.25);">
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">PM10</h2>
                    <div class="flex items-center gap-2">
                        <span data-pm10-icon class="inline-flex items-center justify-center"></span>
                        <span data-pm10-status class="text-lg text-slate-600">Sedang</span>
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

            <div data-pm25-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background-color: rgba(22,163,74,0.25);">
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">PM2.5</h2>
                    <div class="flex items-center gap-2">
                        <span data-pm25-icon class="inline-flex items-center justify-center"></span>
                        <span data-pm25-status class="text-lg text-slate-600">Baik</span>
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

        <div class="mb-6 flex flex-col gap-6">
            <div class="rounded-[15px] border border-slate-200 bg-white p-6 pb-4 shadow-sm">
                <h2 data-chart-title="pm10" class="mb-4 text-2xl font-bold text-slate-900">PM10 - Data Aktual 24 Jam</h2>
                <div class="h-72 w-full sm:h-80"><canvas id="actual-pm10-chart"></canvas></div>
            </div>
            <div class="rounded-[15px] border border-slate-200 bg-white p-6 pb-4 shadow-sm">
                <h2 data-chart-title="pm25" class="mb-4 text-2xl font-bold text-slate-900">PM2.5 - Data Aktual 24 Jam</h2>
                <div class="h-72 w-full sm:h-80"><canvas id="actual-pm25-chart"></canvas></div>
            </div>
        </div>

        <div class="flex flex-col items-start gap-4 rounded-[15px] border border-primary-300 bg-primary-300/25 px-5 py-5 sm:flex-row sm:items-center">
            <div class="flex flex-1 items-start gap-3">
                <div class="relative mt-0.5 shrink-0">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.8125 14.5312C2.81591 17.6382 4.05166 20.6169 6.24861 22.8139C8.44556 25.0108 11.4243 26.2466 14.5312 26.25H24.375C24.8723 26.25 25.3492 26.0525 25.7008 25.7008C26.0525 25.3492 26.25 24.8723 26.25 24.375V14.5312C26.25 11.4232 25.0153 8.44253 22.8177 6.24484C20.62 4.04715 17.6393 2.8125 14.5312 2.8125C11.4232 2.8125 8.44253 4.04715 6.24484 6.24484C4.04715 8.44253 2.8125 11.4232 2.8125 14.5312Z" fill="#0F766E"/></svg>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-50">i</span>
                </div>
                <div>
                    <p class="text-base font-bold text-slate-900">Ingin melihat data secara real-time?</p>
                    <p class="mt-1 text-justify text-sm text-slate-600">Untuk akses data secara real-time, anda bisa mengunjungi website Indeks Standar Pencemaran Udara (ISPU) yang disediakan oleh Dinas Lingkungan Hidup Pusat.</p>
                </div>
            </div>
            <a href="https://ispu.menlhk.go.id/webv5/#/peta/KABUPATEN_INDRAMAYU/-6.327040195465088/108.3219985961914" target="_blank" rel="noopener noreferrer" class="shrink-0 rounded-full bg-primary-300 px-8 py-2.5 text-base font-bold text-slate-50 transition-colors hover:bg-primary-400">Buka Website</a>
        </div>
    </main>

    <script id="actual-pm10-data-24h" type="application/json">[]</script>
    <script id="actual-pm25-data-24h" type="application/json">[]</script>
</div>
@endsection
