@extends('layouts.public')

@section('title', 'Data Aktual')

@section('content')
@php
    $pm10Data24h = [
        ['hour' => 0, 'value' => 30], ['hour' => 1, 'value' => 30], ['hour' => 2, 'value' => 32],
        ['hour' => 3, 'value' => 32], ['hour' => 4, 'value' => 19], ['hour' => 5, 'value' => 38],
        ['hour' => 6, 'value' => 15], ['hour' => 7, 'value' => 48], ['hour' => 8, 'value' => 34],
        ['hour' => 9, 'value' => 27], ['hour' => 10, 'value' => 37], ['hour' => 11, 'value' => 25],
        ['hour' => 12, 'value' => 42], ['hour' => 13, 'value' => 17], ['hour' => 14, 'value' => 16],
        ['hour' => 15, 'value' => 44], ['hour' => 16, 'value' => 35], ['hour' => 17, 'value' => 28],
        ['hour' => 18, 'value' => 32], ['hour' => 19, 'value' => 39], ['hour' => 20, 'value' => 45],
        ['hour' => 21, 'value' => 25], ['hour' => 22, 'value' => 20], ['hour' => 23, 'value' => 17],
    ];

    $pm25Data24h = [
        ['hour' => 0, 'value' => 2], ['hour' => 1, 'value' => 10], ['hour' => 2, 'value' => 9],
        ['hour' => 3, 'value' => 9], ['hour' => 4, 'value' => 14], ['hour' => 5, 'value' => 5],
        ['hour' => 6, 'value' => 3], ['hour' => 7, 'value' => 14], ['hour' => 8, 'value' => 11],
        ['hour' => 9, 'value' => 3], ['hour' => 10, 'value' => 13], ['hour' => 11, 'value' => 13],
        ['hour' => 12, 'value' => 12], ['hour' => 13, 'value' => 2], ['hour' => 14, 'value' => 15],
        ['hour' => 15, 'value' => 6], ['hour' => 16, 'value' => 1], ['hour' => 17, 'value' => 3],
        ['hour' => 18, 'value' => 3], ['hour' => 19, 'value' => 1], ['hour' => 20, 'value' => 14],
        ['hour' => 21, 'value' => 12], ['hour' => 22, 'value' => 3], ['hour' => 23, 'value' => 3],
    ];
@endphp

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

            <div data-date-picker class="relative inline-flex cursor-pointer items-center gap-2 rounded-full border border-primary-300 bg-white px-4 py-1.5">
                <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                <span data-date-display class="text-sm text-slate-600">13 Februari 2025</span>
                <input data-date-input type="date" class="pointer-events-none absolute h-0 w-0 opacity-0" aria-hidden="true">
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
            <a href="https://ispu.menlhk.go.id" target="_blank" rel="noopener noreferrer" class="shrink-0 rounded-full bg-primary-300 px-8 py-2.5 text-base font-bold text-slate-50 transition-colors hover:bg-primary-400">Buka Website</a>
        </div>
    </main>

    <script id="actual-pm10-data-24h" type="application/json">@json($pm10Data24h)</script>
    <script id="actual-pm25-data-24h" type="application/json">@json($pm25Data24h)</script>
</div>
@endsection
