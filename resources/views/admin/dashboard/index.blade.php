@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('admin_page_title', 'Dashboard')

@section('content')
@php
    $baikIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle><circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;

    $sedangIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;

    $totalData = (int) ($dashboard['total_data'] ?? 0);
    $startDate = $dashboard['start_date'] ? \Carbon\Carbon::parse($dashboard['start_date'])->format('d-m-Y') : '-';
    $endDate = $dashboard['end_date'] ? \Carbon\Carbon::parse($dashboard['end_date'])->format('d-m-Y') : '-';
    $missingCount = (int) ($dashboard['missing_count'] ?? 0);
    $duplicateCount = (int) ($dashboard['duplicate_count'] ?? 0);
    $pm10 = $dashboard['pm10'] ?? null;
    $pm25 = $dashboard['pm25'] ?? null;

    $gradientFromHex = static fn (string $hex): string => sprintf(
        'background: linear-gradient(180deg, rgba(%d, %d, %d, 0.25) 0%%, rgba(255, 255, 255, 0.25) 100%%);',
        hexdec(substr($hex, 1, 2)),
        hexdec(substr($hex, 3, 2)),
        hexdec(substr($hex, 5, 2)),
    );
@endphp

<div class="mb-6">
    <h1 class="text-3xl font-bold text-surface-400">Dashboard</h1>
    <p class="mt-1 text-base text-surface-300">Ringkasan dataset dan status prediksi</p>
</div>

<div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-4">
    <div class="rounded-[15px] border border-surface-200 bg-surface-100 px-4 py-4 shadow-sm">
        <h3 class="mb-2 text-xl font-bold text-surface-400">Total data</h3>
        <span class="text-4xl font-normal text-surface-300">{{ number_format($totalData, 0, ',', '.') }}</span>
    </div>

    <div class="rounded-[15px] border border-surface-200 bg-surface-100 px-4 py-4 shadow-sm">
        <h3 class="mb-2 text-xl font-bold text-surface-400">Rentang tanggal data</h3>
        <div class="mt-1 space-y-1 text-base text-surface-300">
            <div class="flex gap-2"><span class="w-10">Start</span><span>:</span><span>{{ $startDate }}</span></div>
            <div class="flex gap-2"><span class="w-10">End</span><span>:</span><span>{{ $endDate }}</span></div>
        </div>
    </div>

    <div class="rounded-[15px] border border-surface-200 bg-surface-100 px-4 py-4 shadow-sm">
        <h3 class="mb-2 text-xl font-bold text-surface-400">Validasi data</h3>
        <div class="mt-1 space-y-1 text-base text-surface-300">
            <div class="flex gap-2"><span class="w-16">Missing</span><span>:</span><span>{{ number_format($missingCount, 0, ',', '.') }}</span></div>
            <div class="flex gap-2"><span class="w-16">Duplikat</span><span>:</span><span>{{ number_format($duplicateCount, 0, ',', '.') }}</span></div>
        </div>
    </div>

    <div class="rounded-[15px] border border-surface-200 bg-surface-100 px-4 py-4 shadow-sm">
        <h3 class="mb-2 text-xl font-bold text-surface-400">Akurasi Prediksi</h3>
        <div class="mt-1 space-y-0.5 text-base text-surface-300">
            <div class="flex gap-2"><span class="w-12">MAE</span><span>:</span><span>0.7727</span></div>
            <div class="flex gap-2"><span class="w-12">RMSE</span><span>:</span><span>0.1231</span></div>
            <div class="flex gap-2"><span class="w-12">R2</span><span>:</span><span>0.1231</span></div>
        </div>
    </div>
</div>

<div class="mb-8">
    <h2 class="mb-4 text-xl font-bold text-surface-400">Ringkasan Data Aktual</h2>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-[15px] border border-surface-200 shadow-sm" style="{{ $gradientFromHex($pm10['category']['hex'] ?? '#2563EB') }}">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM10</span>
                    <div class="flex items-center gap-2">
                        {!! ($pm10['category']['key'] ?? '') === 'baik' ? $baikIconSvg : $sedangIconSvg !!}
                        <span class="text-lg text-surface-300">{{ $pm10['category']['label'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span class="text-sm {{ $pm10['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10['average'] ?? '0,0' }}</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span class="text-sm {{ $pm10['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10['highest'] ?? '0' }}</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span class="text-sm {{ $pm10['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10['lowest'] ?? '0' }}</span></div>
                </div>
            </div>
        </div>

        <div class="rounded-[15px] border border-surface-200 shadow-sm" style="{{ $gradientFromHex($pm25['category']['hex'] ?? '#16A34A') }}">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM2.5</span>
                    <div class="flex items-center gap-2">
                        {!! ($pm25['category']['key'] ?? '') === 'baik' ? $baikIconSvg : $sedangIconSvg !!}
                        <span class="text-lg text-surface-300">{{ $pm25['category']['label'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span class="text-sm {{ $pm25['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25['average'] ?? '0,0' }}</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span class="text-sm {{ $pm25['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25['highest'] ?? '0' }}</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span class="text-sm {{ $pm25['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25['lowest'] ?? '0' }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-8 rounded-[15px] border border-surface-200 bg-surface-100 px-6 py-5">
    <div class="mb-6 flex items-center justify-between gap-3">
        <h2 class="text-xl font-bold text-surface-400">Proses Prediksi</h2>
        <a href="#" class="inline-flex items-center gap-1.5 text-base font-bold text-primary-300 transition-colors hover:text-primary-400">
            Lihat selengkapnya
            <i class="ph ph-arrow-right text-lg"></i>
        </a>
    </div>

    <div class="flex items-start gap-0 overflow-x-auto pb-1">
        @foreach (['Preprocessing', 'Scaling', 'Windowing', 'Training', 'Generate', 'Evaluation'] as $idx => $step)
            <div class="flex items-start">
                <div class="flex min-w-fit flex-col gap-1.5">
                    <span class="whitespace-nowrap text-base font-bold text-surface-300">{{ $step }}</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-base text-ispu-baik">Done</span>
                        <i class="ph-fill ph-check-circle text-xl text-ispu-baik"></i>
                    </div>
                </div>
                @if ($idx < 5)
                    <div class="mx-3 mt-1.5 flex shrink-0 items-center text-primary-300">
                        <i class="ph ph-arrow-right text-2xl"></i>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="ml-6 flex items-center gap-4">
            <div class="h-12 w-0.5 shrink-0 rounded-full bg-primary-300"></div>
            <div class="flex items-center gap-2">
                <span class="text-base font-bold text-surface-300">Status</span>
                <i class="ph-fill ph-check-circle text-xl text-ispu-baik"></i>
            </div>
        </div>
    </div>
</div>

<div>
    <h2 class="mb-4 text-xl font-bold text-surface-400">Ringkasan Prediksi</h2>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-[15px] border border-surface-200 shadow-sm" style="background: linear-gradient(180deg, rgba(37, 99, 235, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM10</span>
                    <div class="flex items-center gap-2">
                        {!! $sedangIconSvg !!}
                        <span class="text-lg text-surface-300">Sedang</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span class="text-sm text-ispu-sedang">47,5</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span class="text-sm text-ispu-sedang">61</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span class="text-sm text-ispu-sedang">41</span></div>
                </div>
            </div>
        </div>

        <div class="rounded-[15px] border border-surface-200 shadow-sm" style="background: linear-gradient(180deg, rgba(22, 163, 74, 0.25) 0%, rgba(255, 255, 255, 0.25) 100%);">
            <div class="px-8 py-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-2xl font-bold text-surface-400">PM2.5</span>
                    <div class="flex items-center gap-2">
                        {!! $baikIconSvg !!}
                        <span class="text-lg text-surface-300">Baik</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Rata-rata:</span><span class="text-sm text-ispu-baik">47,5</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Tertinggi:</span><span class="text-sm text-ispu-baik">61</span></div>
                    <div class="flex items-center gap-1 rounded-[10px] border border-surface-200 bg-transparent px-2 py-1.5"><span class="text-sm text-surface-300">Terendah:</span><span class="text-sm text-ispu-baik">41</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
