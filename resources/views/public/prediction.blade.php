@extends('layouts.public')

@section('title', 'Prediksi')

@section('content')
@php
    $pm10Data24h = [
        ['hour' => 0, 'value' => 30], ['hour' => 1, 'value' => 31], ['hour' => 2, 'value' => 32],
        ['hour' => 3, 'value' => 30], ['hour' => 4, 'value' => 20], ['hour' => 5, 'value' => 15],
        ['hour' => 6, 'value' => 48], ['hour' => 7, 'value' => 35], ['hour' => 8, 'value' => 30],
        ['hour' => 9, 'value' => 25], ['hour' => 10, 'value' => 17], ['hour' => 11, 'value' => 16],
        ['hour' => 12, 'value' => 25], ['hour' => 13, 'value' => 42], ['hour' => 14, 'value' => 16],
        ['hour' => 15, 'value' => 44], ['hour' => 16, 'value' => 28], ['hour' => 17, 'value' => 18],
        ['hour' => 18, 'value' => 39], ['hour' => 19, 'value' => 28], ['hour' => 20, 'value' => 37],
        ['hour' => 21, 'value' => 46], ['hour' => 22, 'value' => 26], ['hour' => 23, 'value' => 18],
    ];

    $pm25Data24h = [
        ['hour' => 0, 'value' => 2], ['hour' => 1, 'value' => 9], ['hour' => 2, 'value' => 8],
        ['hour' => 3, 'value' => 14], ['hour' => 4, 'value' => 14], ['hour' => 5, 'value' => 11],
        ['hour' => 6, 'value' => 5], ['hour' => 7, 'value' => 3], ['hour' => 8, 'value' => 14],
        ['hour' => 9, 'value' => 14], ['hour' => 10, 'value' => 13], ['hour' => 11, 'value' => 13],
        ['hour' => 12, 'value' => 3], ['hour' => 13, 'value' => 14], ['hour' => 14, 'value' => 2],
        ['hour' => 15, 'value' => 15], ['hour' => 16, 'value' => 3], ['hour' => 17, 'value' => 3],
        ['hour' => 18, 'value' => 8], ['hour' => 19, 'value' => 0], ['hour' => 20, 'value' => 3],
        ['hour' => 21, 'value' => 14], ['hour' => 22, 'value' => 13], ['hour' => 23, 'value' => 3],
    ];
@endphp

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
                <button data-period-btn="12 Jam" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">12 Jam</button>
                <button data-period-btn="24 Jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm font-normal text-surface-50 transition-all">24 Jam</button>
                <button data-period-btn="7 Hari" class="rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">7 Hari</button>
            </div>

            <div data-date-picker class="relative inline-flex cursor-pointer items-center gap-2 rounded-full border border-primary-300 bg-white px-4 py-1.5">
                <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                <span data-date-display class="text-sm text-slate-600">13 Februari 2025</span>
                <input data-date-input type="date" class="pointer-events-none absolute h-0 w-0 opacity-0" aria-hidden="true">
            </div>
        </div>

        <div class="mb-5 flex flex-col gap-4 sm:flex-row">
            <div data-pm10-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background-color: rgba(37, 99, 235, 0.25);">
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

            <div data-pm25-card class="min-w-0 flex-1 rounded-[15px] border border-slate-200 p-6 shadow-sm" style="background-color: rgba(22, 163, 74, 0.25);">
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

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto]">
            <div class="min-w-0">
                <div class="mb-4 rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 data-chart-title="pm10" class="mb-4 text-xl font-bold text-slate-900">PM10 - Prediksi 24 Jam Terakhir</h3>
                    <div class="h-[280px] w-full"><canvas id="pm10-chart"></canvas></div>
                </div>
                <div class="rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 data-chart-title="pm25" class="mb-4 text-xl font-bold text-slate-900">PM2.5 - Prediksi 24 Jam Terakhir</h3>
                    <div class="h-[280px] w-full"><canvas id="pm25-chart"></canvas></div>
                </div>
            </div>

            <div class="w-full shrink-0 lg:w-[370px]">
                <div class="h-full rounded-[15px] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-xl font-bold text-slate-900">Detail Prediksi</h3>

                    <div class="relative mb-4 inline-flex items-center rounded-full border border-primary-300 bg-white p-[3px]">
                        <button data-tab="pm10" class="relative z-10 rounded-full px-4 py-1.5 text-sm font-normal text-slate-600 transition-all">PM10</button>
                        <button data-tab="pm25" class="relative z-10 rounded-full bg-primary-300 px-4 py-1.5 text-sm font-normal text-surface-50 transition-all">PM2.5</button>
                    </div>

                    <div class="max-h-[560px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-t-2 border-primary-300">
                                    <th class="py-2 text-center font-normal text-slate-600">Jam</th>
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

    <script id="pm10-data-24h" type="application/json">@json($pm10Data24h)</script>
    <script id="pm25-data-24h" type="application/json">@json($pm25Data24h)</script>
</div>
@endsection
