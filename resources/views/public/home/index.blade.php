@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
@php
    $payload = $predictionPayload ?? [
        'window_title' => 'Ringkasan Prediksi 6 Jam ke Depan',
        'window_date_label' => '-',
        'labels' => [],
        'pm10' => ['series' => [], 'summary' => ['average' => '0,0', 'highest' => 0, 'lowest' => 0, 'category' => ['key' => 'sedang', 'label' => '-', 'hex' => '#2563EB', 'text_class' => 'text-surface-300']]],
        'pm25' => ['series' => [], 'summary' => ['average' => '0,0', 'highest' => 0, 'lowest' => 0, 'category' => ['key' => 'sedang', 'label' => '-', 'hex' => '#2563EB', 'text_class' => 'text-surface-300']]],
    ];

    $pm10Summary = $payload['pm10']['summary'] ?? [];
    $pm25Summary = $payload['pm25']['summary'] ?? [];
    $hasActiveRun = (bool) ($payload['has_active_run'] ?? false);

    $severityRank = [
        'baik' => 1,
        'sedang' => 2,
        'tidakSehat' => 3,
        'sangatTidakSehat' => 4,
        'berbahaya' => 5,
    ];

    $adviceByCategory = [
        'baik' => 'Kualitas udara relatif baik, silakan beraktivitas seperti biasa. Tetap cek informasi secara berkala.',
        'sedang' => 'Kualitas udara sedang, aktivitas luar ruang masih dapat dilakukan dengan tetap waspada dan memantau kondisi.',
        'tidakSehat' => 'Kualitas udara tidak sehat, kurangi aktivitas luar ruang dalam durasi lama dan pertimbangkan penggunaan pelindung.',
        'sangatTidakSehat' => 'Kualitas udara sangat tidak sehat, batasi aktivitas luar ruang dan prioritaskan berada di dalam ruangan.',
        'berbahaya' => 'Kualitas udara berbahaya, hindari aktivitas luar ruang dan ikuti arahan resmi dari instansi terkait.',
    ];

    if ($hasActiveRun) {
        $pm10CategoryKey = $pm10Summary['category']['key'] ?? 'sedang';
        $pm25CategoryKey = $pm25Summary['category']['key'] ?? 'sedang';
        $pm10Level = $severityRank[$pm10CategoryKey] ?? $severityRank['sedang'];
        $pm25Level = $severityRank[$pm25CategoryKey] ?? $severityRank['sedang'];
        $dominantCategory = $pm10Level >= $pm25Level ? $pm10CategoryKey : $pm25CategoryKey;
        $homeAdvice = $adviceByCategory[$dominantCategory]
            ?? 'Silakan cek informasi kualitas udara secara berkala.';
    } else {
        $homeAdvice = 'Prediksi belum tersedia saat ini. Silakan cek kembali setelah proses prediksi dijalankan oleh admin.';
    }
@endphp

@php
    $baikIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle><circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;

    $sedangIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>
SVG;

    $tidakSehatIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#F0B100" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.4 145.2s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8S386.9 493 227.7 493 47.9 337.6 47.9 337.6s-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.3" cy="289.5" r="25.7" fill="#F0B100"></circle><circle cx="135.2" cy="289.5" r="25.7" fill="#F0B100"></circle><path stroke="#F0B100" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.6 420.7s14.3-29.6 48.6-29.6c22 0 35.3 9.3 48.6 29.6M32.1 266.8S14.6 156.3 64.2 96.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5C222.6-8 443.8.8 423.5 265.2"></path></svg>
SVG;

    $sangatTidakSehatIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="red"></circle><circle cx="135" cy="288.829" r="25.7" fill="red"></circle><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M76.8 340.829v72c24.8 39.4 70 79.5 150.7 79.5 80.8 0 125.2-40.3 149.3-79.9v-71.6s-98.493-24.121-150-24.121-150 24.121-150 24.121" clip-rule="evenodd"></path><rect width="60" height="60" x="197.8" y="374.329" stroke="red" stroke-width="10" rx="10"></rect><path stroke="red" stroke-linecap="round" stroke-width="10" d="M193.8 348.351s33.35-10.52 66.7 0"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M76.8 340.829S178.794 318.3 229.136 318.3 376.8 340.829 376.8 340.829l19.5-85.3"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M376.8 340.829s-98.036-22.57-148.233-22.57S76.8 340.829 76.8 340.829l-19.5-85.3"></path><circle cx="228" cy="404.529" r="17.2" fill="red" stroke="red" stroke-width="10"></circle></svg>
SVG;

    $berbahayaIconSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 510" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M70.8 402.529c-18.9-34.4-23.1-65.6-23.1-65.6s-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.4 240.5 0 0 0 28.1 4.4 26.6 34.8s-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-2.7 30.1-21 64.6M290.2 482.729c-18 6-38.7 9.6-62.7 9.6-24.3 0-45.5-3.6-63.7-9.8"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0F172A"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0F172A"></circle><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M397.2 367.029c-15.4-21.1-100.3-47.6-118.7-54.2-18.8-6.8-46.3-6.6-51.7-6.5h-.2c-5.4-.1-32.9-.2-51.7 6.5-18.6 6.7-105.9 33.9-119.3 55.2"></path><ellipse cx="99.72" cy="448.953" stroke="#0F172A" stroke-width="20" rx="26" ry="55.101" transform="rotate(-28.092 99.72 448.953)"></ellipse><path stroke="#0F172A" stroke-width="15" d="M117.9 376.729c12.7-6.8 34.5 9.5 48.9 36.4 14.3 26.9 15.7 54.1 3.1 60.9M117.9 376.729l-44.1 23.5M169.8 474.029l-44.1 23.5"></path><ellipse cx="353.905" cy="448.87" stroke="#0F172A" stroke-width="20" rx="55.101" ry="26" transform="rotate(-61.908 353.905 448.87)"></ellipse><path stroke="#0F172A" stroke-width="15" d="M335.7 376.729c-12.7-6.8-34.5 9.5-48.9 36.4-14.3 26.9-15.7 54.1-3.1 60.9M335.7 376.729l44.1 23.5M283.8 474.029l44.1 23.5"></path><circle cx="226.8" cy="408.929" r="21.2" stroke="#0F172A" stroke-width="10"></circle><path stroke="#0F172A" stroke-linecap="round" stroke-width="10" d="M193.4 338.329s37.4-15.8 66.7 0M202.9 358.929s26.8-10.8 47.8 0"></path></svg>
SVG;
@endphp

<div data-page="public-home" class="min-h-screen bg-surface-50">
    <div class="mx-auto flex w-full max-w-[1192px] flex-col gap-6 px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="text-center">
            <h1 class="text-2xl font-bold leading-tight text-surface-400 sm:text-3xl lg:text-[32px]">
                Kualitas Udara Kabupaten Indramayu
            </h1>
            <p class="mt-3 text-sm font-normal text-surface-300 sm:text-base">
                Informasi kualitas udara konsentrasi partikulat PM10 dan PM2.5 di Kabupaten Indramayu
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-3">
            <div class="flex min-w-[160px] max-w-[230px] flex-1 items-center gap-3 rounded-[10px] border border-ispu-baik bg-[rgba(22,163,74,0.25)] px-4 py-3 shadow-sm">
                {!! $baikIconSvg !!}
                <div>
                    <p class="text-xs leading-tight text-surface-300">0-15.5 µg/m³</p>
                    <p class="mt-0.5 text-xs font-semibold leading-tight text-ispu-baik">Baik</p>
                </div>
            </div>
            <div class="flex min-w-[160px] max-w-[230px] flex-1 items-center gap-3 rounded-[10px] border border-ispu-sedang bg-[rgba(37,99,235,0.25)] px-4 py-3 shadow-sm">
                {!! $sedangIconSvg !!}
                <div>
                    <p class="text-xs leading-tight text-surface-300">15.6-55.4 µg/m³</p>
                    <p class="mt-0.5 text-xs font-semibold leading-tight text-ispu-sedang">Sedang</p>
                </div>
            </div>
            <div class="flex min-w-[160px] max-w-[230px] flex-1 items-center gap-3 rounded-[10px] border border-ispu-tidak-sehat bg-[rgba(250,204,21,0.25)] px-4 py-3 shadow-sm">
                {!! $tidakSehatIconSvg !!}
                <div>
                    <p class="text-xs leading-tight text-surface-300">55.5-150.4 µg/m³</p>
                    <p class="mt-0.5 text-xs font-semibold leading-tight text-ispu-tidak-sehat">Tidak Sehat</p>
                </div>
            </div>
            <div class="flex min-w-[160px] max-w-[230px] flex-1 items-center gap-3 rounded-[10px] border border-ispu-sangat-tidak-sehat bg-[rgba(220,38,38,0.25)] px-4 py-3 shadow-sm">
                {!! $sangatTidakSehatIconSvg !!}
                <div>
                    <p class="text-xs leading-tight text-surface-300">150.5-250.4 µg/m³</p>
                    <p class="mt-0.5 text-xs font-semibold leading-tight text-ispu-sangat-tidak-sehat">Sangat Tidak Sehat</p>
                </div>
            </div>
            <div class="flex min-w-[160px] max-w-[230px] flex-1 items-center gap-3 rounded-[10px] border border-ispu-berbahaya bg-[rgba(17,24,39,0.15)] px-4 py-3 shadow-sm">
                {!! $berbahayaIconSvg !!}
                <div>
                    <p class="text-xs leading-tight text-surface-300">&gt;250.5 µg/m³</p>
                    <p class="mt-0.5 text-xs font-semibold leading-tight text-ispu-berbahaya">Berbahaya</p>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-[15px] border border-surface-200 bg-white shadow-sm">
            <div class="absolute bottom-0 left-0 top-0 w-[13px] rounded-l-[15px] bg-primary-300"></div>

            <div class="px-5 py-5 pl-[30px]">
                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-xl font-bold text-surface-400 sm:text-2xl">{{ $payload['window_title'] }}</h2>
                    <a href="{{ route('public.prediction') }}" class="flex items-center gap-1 text-base font-bold text-primary-300 hover:underline">
                        <span>Lihat prediksi lengkap</span>
                        <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
                @if (! $hasActiveRun)
                    <div class="mb-3 rounded-[10px] border border-[#F59E0B] bg-[#FFFBEB] px-3 py-2 text-sm font-medium text-[#B45309]">
                        Prediksi belum tersedia. Data prediksi akan tampil setelah admin mengaktifkan hasil run prediksi.
                    </div>
                @endif
                <p class="mb-3 text-sm font-medium text-primary-300">{{ $payload['window_date_label'] ?? '-' }}</p>

                <div class="flex flex-col gap-6 sm:flex-row">
                    <div class="flex-1 px-1 py-1">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="text-lg font-bold text-surface-400 sm:text-xl">PM10</span>
                            {!! (($pm10Summary['category']['key'] ?? '') === 'baik') ? $baikIconSvg : $sedangIconSvg !!}
                            <span class="text-base font-normal text-surface-300 sm:text-lg">{{ $pm10Summary['category']['label'] ?? '-' }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Rata-rata:</span> <span class="ml-1 {{ $pm10Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10Summary['average'] ?? '0,0' }}</span></div>
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Tertinggi:</span> <span class="ml-1 {{ $pm10Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10Summary['highest'] ?? 0 }}</span></div>
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Terendah:</span> <span class="ml-1 {{ $pm10Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm10Summary['lowest'] ?? 0 }}</span></div>
                        </div>
                    </div>

                    <div class="hidden w-[3px] self-stretch rounded-[10px] bg-primary-300 sm:block"></div>
                    <div class="block h-px bg-surface-200 sm:hidden"></div>

                    <div class="flex-1 px-1 py-1">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="text-lg font-bold text-surface-400 sm:text-xl">PM2.5</span>
                            {!! (($pm25Summary['category']['key'] ?? '') === 'baik') ? $baikIconSvg : $sedangIconSvg !!}
                            <span class="text-base font-normal text-surface-300 sm:text-lg">{{ $pm25Summary['category']['label'] ?? '-' }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Rata-rata:</span> <span class="ml-1 {{ $pm25Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25Summary['average'] ?? '0,0' }}</span></div>
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Tertinggi:</span> <span class="ml-1 {{ $pm25Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25Summary['highest'] ?? 0 }}</span></div>
                            <div class="rounded-[10px] border border-surface-200 px-2 py-2 text-sm sm:text-base"><span class="text-surface-300">Terendah:</span> <span class="ml-1 {{ $pm25Summary['category']['text_class'] ?? 'text-surface-300' }}">{{ $pm25Summary['lowest'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="flex flex-col gap-4 rounded-[15px] border border-surface-200 bg-white p-5 shadow-sm sm:p-7">
                <h3 class="text-lg font-bold text-surface-400 sm:text-2xl">PM10 - Grafik {{ $payload['window_title'] }}</h3>
                <div class="h-[280px] w-full"><canvas id="public-home-pm10-chart"></canvas></div>
            </div>

            <div class="flex flex-col gap-4 rounded-[15px] border border-surface-200 bg-white p-5 shadow-sm sm:p-7">
                <h3 class="text-lg font-bold text-surface-400 sm:text-2xl">PM2.5 - Grafik {{ $payload['window_title'] }}</h3>
                <div class="h-[280px] w-full"><canvas id="public-home-pm25-chart"></canvas></div>
            </div>
        </div>

        <div class="flex items-start gap-3 rounded-[15px] border border-primary-300 bg-[rgba(15,118,110,0.25)] px-5 py-4">
            <div class="mt-0.5 flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-full bg-primary-300 text-base font-bold text-surface-50">i</div>
            <p class="text-sm font-bold leading-snug text-surface-400 sm:text-lg">
                {{ $homeAdvice }}
            </p>
        </div>
    </div>

    <script id="home-prediction-labels" type="application/json">@json($payload['labels'] ?? [])</script>
    <script id="home-pm10-series" type="application/json">@json($payload['pm10']['series'] ?? [])</script>
    <script id="home-pm25-series" type="application/json">@json($payload['pm25']['series'] ?? [])</script>
    <script id="home-pm10-color" type="application/json">@json($pm10Summary['category']['hex'] ?? '#2563EB')</script>
    <script id="home-pm25-color" type="application/json">@json($pm25Summary['category']['hex'] ?? '#16A34A')</script>
</div>
@endsection
