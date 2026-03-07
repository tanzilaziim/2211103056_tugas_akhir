@extends('layouts.admin')

@section('title', 'Prediksi Grafik Admin')
@section('admin_page_title', 'Prediksi - Grafik')

@section('content')
@php
    $baikIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="h-6 w-6"><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle><circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;

    $sedangIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="h-6 w-6"><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;
@endphp

<div data-page="admin-prediction-chart" class="min-h-full bg-surface-50">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-surface-400">Hasil Prediksi</h1>
        <p class="mt-1 text-base text-surface-300">Ringkasan grafik hasil prediksi</p>
    </div>

    @include('admin.prediction._tabs')

    <h2 class="mb-4 text-2xl font-bold text-surface-400">Grafik Prediksi</h2>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div class="relative">
            <button type="button" data-chart-date-toggle class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                <span data-chart-date-label>13 Februari 2025</span>
            </button>
            <div data-chart-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
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
        </div>

        <div class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2">
            <div class="relative">
                <button type="button" data-chart-indicator-toggle class="inline-flex min-w-[110px] items-center justify-between gap-2 text-sm text-surface-300">
                    <span data-chart-indicator-label>Semua</span>
                    <i class="ph ph-caret-down text-base text-primary-300"></i>
                </button>
                <div data-chart-indicator-menu class="absolute right-0 top-full z-20 mt-2 hidden w-36 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg">
                    <button type="button" data-chart-indicator-option="Semua" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">Semua</button>
                    <button type="button" data-chart-indicator-option="PM10" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">PM10</button>
                    <button type="button" data-chart-indicator-option="PM2.5" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">PM2.5</button>
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
                        {!! $sedangIconSvg !!}
                        <span class="text-lg text-surface-300">Sedang</span>
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
                        {!! $baikIconSvg !!}
                        <span class="text-lg text-surface-300">Baik</span>
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
@endsection
