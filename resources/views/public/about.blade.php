@extends('layouts.public')

@section('title', 'Tentang')

@section('content')
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

    $ispuCards = [
        ['range' => '0-15.5 µg/m³', 'label' => 'Baik', 'color' => '#16A34A', 'bg' => 'rgba(22,163,74,0.25)', 'iconSvg' => $baikIconSvg],
        ['range' => '15.6-55.4 µg/m³', 'label' => 'Sedang', 'color' => '#2563EB', 'bg' => 'rgba(37,99,235,0.25)', 'iconSvg' => $sedangIconSvg],
        ['range' => '55.5-150.4 µg/m³', 'label' => 'Tidak Sehat', 'color' => '#FACC15', 'bg' => 'rgba(250,204,21,0.25)', 'iconSvg' => $tidakSehatIconSvg],
        ['range' => '150.5-250.4 µg/m³', 'label' => 'Sangat Tidak Sehat', 'color' => '#DC2626', 'bg' => 'rgba(220,38,38,0.25)', 'iconSvg' => $sangatTidakSehatIconSvg],
        ['range' => '>250.5 µg/m³', 'label' => 'Berbahaya', 'color' => '#111827', 'bg' => 'rgba(17,24,39,0.15)', 'iconSvg' => $berbahayaIconSvg],
    ];

    $airQualityCategories = [
        [
            'kategori' => 'Baik',
            'kondisi' => 'Udara relatif bersih',
            'saranUmum' => 'Aktivitas normal',
            'saranSensitif' => 'Aktivitas normal',
            'borderColor' => 'border-[#16A34A]',
            'bgColor' => 'bg-[#16A34A]/15',
        ],
        [
            'kategori' => 'Sedang',
            'kondisi' => 'Masih relatif bersih namun perlu waspada',
            'saranUmum' => 'Aktivitas normal, tetap waspada',
            'saranSensitif' => 'Kurangi aktivitas berat di luar',
            'borderColor' => 'border-[#2563EB]',
            'bgColor' => 'bg-[#2563EB]/15',
        ],
        [
            'kategori' => 'Tidak Sehat',
            'kondisi' => 'Relatif tercemar, mulai berdampak bagi sebagian orang',
            'saranUmum' => 'Kurangi aktivitas di luar dalam waktu lama',
            'saranSensitif' => 'Batasi aktivitas luar, gunakan perlindungan bila perlu',
            'borderColor' => 'border-[#FACC15]',
            'bgColor' => 'bg-[#FACC15]/15',
        ],
        [
            'kategori' => 'Sangat Tidak Sehat',
            'kondisi' => 'Udara tercemar, berisiko bagi banyak orang',
            'saranUmum' => 'Hindari aktivitas di luar, pilih ruang tertutup',
            'saranSensitif' => 'Tetap di dalam ruangan, minimalkan paparan',
            'borderColor' => 'border-[#DC2626]',
            'bgColor' => 'bg-[#DC2626]/15',
        ],
        [
            'kategori' => 'Berbahaya',
            'kondisi' => 'Udara kotor berisiko tinggi',
            'saranUmum' => 'Tetap di dalam ruangan, hentikan aktivitas luar',
            'saranSensitif' => 'Prioritaskan keamanan, ikuti arahan resmi instansi',
            'borderColor' => 'border-[#111827]',
            'bgColor' => 'bg-[#111827]/10',
        ],
    ];

    $faqs = [
        [
            'question' => 'Kenapa konsentrasi polutan bisa berubah-ubah dengan cepat?',
            'answer' => 'Konsentrasi polutan dipengaruhi cuaca, arah dan kecepatan angin, aktivitas transportasi, serta aktivitas pembakaran. Karena faktor tersebut berubah dalam waktu singkat, nilai polutan juga bisa naik-turun cepat.',
        ],
        [
            'question' => 'Apa arti µg/m³?',
            'answer' => 'µg/m³ adalah satuan konsentrasi partikel di udara, yaitu mikrogram per meter kubik. Nilai ini menunjukkan berapa banyak massa partikel polutan yang terkandung dalam satu meter kubik udara.',
        ],
        [
            'question' => 'Apa bedanya PM2.5 dan PM10?',
            'answer' => 'PM2.5 berukuran lebih kecil dari PM10 sehingga lebih mudah masuk jauh ke paru-paru. PM10 cenderung tertahan di saluran napas atas, sedangkan PM2.5 punya potensi dampak kesehatan yang lebih tinggi.',
        ],
        [
            'question' => 'Kapan sebaiknya membatasi aktivitas luar ruang?',
            'answer' => 'Sebaiknya mulai membatasi aktivitas luar saat kualitas udara masuk kategori Tidak Sehat atau lebih buruk. Kelompok sensitif disarankan membatasi lebih awal.',
        ],
    ];
@endphp

<div class="min-h-screen bg-white font-[Inter,sans-serif]">
    <main class="mx-auto w-full max-w-[1192px] space-y-8 px-4 py-8 sm:space-y-10 sm:px-6 sm:py-12 lg:px-8">
        <section class="mb-10 text-center sm:mb-14">
            <h1 class="mb-4 text-2xl font-bold leading-snug text-surface-400 sm:text-3xl">
                Tentang Sistem Informasi Kualitas Udara<br>
                Kabupaten Indramayu
            </h1>
            <p class="mx-auto mb-5 max-w-3xl text-sm text-surface-300 sm:text-base">
                Website ini menyajikan informasi konsentrasi partikulat PM10 dan PM2.5 serta panduan mitigasi
                berdasarkan kategori kualitas udara.
            </p>

            <div class="mb-3 inline-flex items-center gap-3 rounded-[10px] border border-surface-200 bg-white px-4 py-3">
                <img
                    src="{{ Vite::asset('resources/assets/images/logo_bplh.png') }}"
                    alt="Logo Dinas Lingkungan Hidup"
                    class="h-[45px] w-[45px] shrink-0 object-contain"
                >
                <span class="text-sm text-surface-300 sm:text-base">Disediakan oleh: Dinas Lingkungan Hidup Kabupaten Indramayu</span>
            </div>

            <p class="text-xs text-surface-300 sm:text-sm">
                Informasi ditujukan untuk edukasi dan kewaspadaan, mohon ikuti arahan resmi instansi terkait.
            </p>
        </section>

        <section class="mb-10 sm:mb-14">
            <h2 class="mb-5 text-xl font-bold text-surface-400 sm:text-2xl">Apa itu Pencemaran Udara?</h2>
            <div class="flex flex-col gap-5 lg:flex-row">
                <div class="flex-[3] rounded-[15px] border border-primary-300 bg-white p-5">
                    <h3 class="mb-2 text-lg font-bold text-surface-400 sm:text-xl">Definisi Singkat</h3>
                    <p class="mb-6 text-justify text-sm text-surface-400">
                        Pencemaran udara adalah kondisi saat udara menjadi kotor dan tidak sehat karena tercampur zat
                        berbahaya seperti asap, debu, atau gas beracun yang bisa membuat manusia, hewan, dan tanaman
                        menjadi terganggu kesehatannya.
                    </p>
                    <h3 class="mb-2 text-lg font-bold text-surface-400 sm:text-xl">Dampak Umum</h3>
                    <ul class="list-inside list-disc space-y-1 text-sm text-surface-400">
                        <li>Mengganggu kenyamanan dan aktivitas di luar ruangan.</li>
                        <li>Dapat mengganggu sistem pernapasan.</li>
                        <li>Risiko gangguan kesehatan meningkat ketika konsentrasi partikulat tinggi.</li>
                    </ul>
                </div>

                <div class="flex-[2] rounded-[15px] border border-primary-300 bg-white p-5">
                    <h3 class="mb-5 text-lg font-bold text-surface-400 sm:text-xl">Penyebab Utama Peningkatan Pencemaran</h3>
                    <div class="space-y-5 text-sm text-surface-400">
                        <div class="flex items-start gap-3">
                            <i class="ph-bold ph-wind text-3xl text-primary-300"></i>
                            <span>Debu jalan dan angin kencang</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ph-bold ph-fire text-3xl text-primary-300"></i>
                            <span>Pembakaran terbuka/asap</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ph-bold ph-truck text-3xl text-primary-300"></i>
                            <span>Aktivitas transportasi dan industri</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-10 sm:mb-14">
            <h2 class="mb-5 text-xl font-bold text-surface-400 sm:text-2xl">Mengenal Polutan PM10 dan PM2.5</h2>

            <div class="mb-5 rounded-[15px] border border-primary-300 bg-primary-50 p-5">
                <h3 class="mb-2 text-base font-bold text-surface-400 sm:text-lg">Apa itu Particulate Matter (PM)?</h3>
                <p class="text-justify text-xs text-surface-300 sm:text-sm">
                    Particulate Matter (PM) adalah campuran partikel padat dan tetesan cair yang melayang di udara,
                    terdiri dari debu, jelaga, asap, dan bahan kimia lainnya. PM berasal dari transportasi, industri,
                    dan pembakaran. Ukuran PM yang kecil, terutama PM2.5, membuatnya lebih mudah terhirup ke dalam
                    saluran pernapasan sehingga dapat meningkatkan risiko gangguan kesehatan.
                </p>
            </div>

            <div class="flex flex-col gap-5 sm:flex-row">
                <div class="flex-1 rounded-[15px] border border-primary-300 p-5" style="background: linear-gradient(180deg, #F0FDFA 50%, #FFFFFF 100%);">
                    <h3 class="mb-3 text-lg font-bold text-surface-400 sm:text-xl">PM10</h3>
                    <ul class="list-inside list-disc space-y-1 text-sm text-surface-400">
                        <li>Ukuran partikel relatif lebih besar (debu/partikel kasar)</li>
                        <li>Dapat mengiritasi saluran napas</li>
                        <li>Sering meningkat karena debu, lalu lintas, dan konstruksi</li>
                    </ul>
                </div>

                <div class="flex-1 rounded-[15px] border border-primary-300 p-5" style="background: linear-gradient(180deg, #F0FDFA 40%, #FFFFFF 100%);">
                    <h3 class="mb-3 text-lg font-bold text-surface-400 sm:text-xl">PM2.5</h3>
                    <ul class="list-inside list-disc space-y-1 text-sm text-surface-400">
                        <li>Ukuran partikel sangat kecil</li>
                        <li>Lebih mudah masuk jauh ke saluran napas</li>
                        <li>Umumnya berasal dari asap/pembakaran dan reaksi kimia di udara</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="mb-10 sm:mb-14">
            <h2 class="mb-5 text-xl font-bold text-surface-400 sm:text-2xl">Kategori Indeks Standar Pencemaran Udara</h2>
            <div class="rounded-[15px] border border-primary-300 bg-white px-5 py-6">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($ispuCards as $cat)
                        <div
                            class="flex items-center gap-3 rounded-[10px] px-3 py-4 shadow-sm"
                            style="border: 1px solid {{ $cat['color'] }}; background: {{ $cat['bg'] }};"
                        >
                            <div class="shrink-0">
                                {!! $cat['iconSvg'] !!}
                            </div>
                            <div>
                                <p class="text-[11px] leading-tight text-surface-300">{{ $cat['range'] }}</p>
                                <p class="mt-0.5 text-xs font-semibold leading-tight" style="color: {{ $cat['color'] }};">{{ $cat['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="space-y-8 sm:space-y-10">
            <div>
                <h2 class="mb-4 text-2xl font-bold text-surface-400">Apa yang Harus Dilakukan?</h2>

                <div class="w-full overflow-hidden rounded-[15px] border border-primary-300">
                    <div class="bg-primary-300 px-4 py-3">
                        <div class="hidden grid-cols-4 gap-4 sm:grid">
                            <span class="text-base font-bold text-surface-50 lg:text-lg">Kategori</span>
                            <span class="text-base font-bold text-surface-50 lg:text-lg">Kondisi</span>
                            <span class="text-base font-bold text-surface-50 lg:text-lg">Saran untuk Semua Orang</span>
                            <span class="text-base font-bold text-surface-50 lg:text-lg">Saran untuk Kelompok Sensitif</span>
                        </div>
                        <div class="sm:hidden text-base font-bold text-surface-50">Panduan per Kategori</div>
                    </div>

                    @foreach ($airQualityCategories as $row)
                        <div class="border-b border-l border-r px-4 py-4 {{ $row['bgColor'] }} {{ $row['borderColor'] }} @if ($loop->last) border-b @endif">
                            <div class="hidden grid-cols-4 gap-4 sm:grid">
                                <span class="text-base text-surface-300">{{ $row['kategori'] }}</span>
                                <span class="text-base text-surface-300">{{ $row['kondisi'] }}</span>
                                <span class="text-base text-surface-300">{{ $row['saranUmum'] }}</span>
                                <span class="text-base text-surface-300">{{ $row['saranSensitif'] }}</span>
                            </div>

                            <div class="space-y-1 sm:hidden">
                                <p class="text-sm font-semibold text-surface-300">{{ $row['kategori'] }}</p>
                                <p class="text-xs text-surface-300"><span class="font-medium">Kondisi:</span> {{ $row['kondisi'] }}</p>
                                <p class="text-xs text-surface-300"><span class="font-medium">Semua Orang:</span> {{ $row['saranUmum'] }}</p>
                                <p class="text-xs text-surface-300"><span class="font-medium">Kelompok Sensitif:</span> {{ $row['saranSensitif'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full rounded-[15px] border border-primary-300 bg-primary-300/25 px-4 py-4">
                <div class="flex items-start gap-3">
                    <div class="relative h-[30px] w-[30px] shrink-0">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.8125 14.5312C2.81591 17.6382 4.05166 20.6169 6.24861 22.8139C8.44556 25.0108 11.4243 26.2466 14.5312 26.25H24.375C24.8723 26.25 25.3492 26.0525 25.7008 25.7008C26.0525 25.3492 26.25 24.8723 26.25 24.375V14.5312C26.25 11.4232 25.0153 8.44253 22.8177 6.24484C20.62 4.04715 17.6393 2.8125 14.5312 2.8125C11.4232 2.8125 8.44253 4.04715 6.24484 6.24484C4.04715 8.44253 2.8125 11.4232 2.8125 14.5312Z" fill="#0F766E"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-base font-bold leading-none text-white" style="padding-top:1px;">i</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="mb-2 text-lg font-bold text-surface-400">Siapa saja kategori kelompok sensitif?</p>
                        <p class="text-xs leading-relaxed text-surface-300 sm:text-sm">
                            Kelompok sensitif adalah kelompok yang lebih berisiko mengalami gangguan kesehatan saat kualitas udara menurun,
                            seperti anak-anak, lansia, ibu hamil, serta penderita penyakit pernapasan atau jantung.
                            Kelompok ini disarankan lebih cepat membatasi aktivitas luar ruang ketika kategori udara masuk Tidak Sehat atau lebih buruk.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mb-4 text-2xl font-bold text-surface-400">Pertanyaan Umum</h2>

                <div class="space-y-3 rounded-[15px] border border-surface-200 bg-white p-4 sm:p-6">
                    @foreach ($faqs as $faq)
                        <div class="overflow-hidden rounded-[15px] border border-primary-300 bg-surface-50" data-faq-item>
                            <button type="button" class="flex w-full items-center justify-between px-5 py-[13px] text-left" data-faq-toggle aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="pr-4 text-base font-bold text-surface-300">{{ $faq['question'] }}</span>
                                <span class="inline-flex items-center">
                                    <i class="ph-bold ph-caret-up text-xl text-surface-400 {{ $loop->first ? '' : 'hidden' }}" data-caret-up></i>
                                    <i class="ph-bold ph-caret-down text-xl text-surface-400 {{ $loop->first ? 'hidden' : '' }}" data-caret-down></i>
                                </span>
                            </button>
                            <div class="px-5 pb-4 {{ $loop->first ? '' : 'hidden' }}" data-faq-panel>
                                <p class="text-justify text-sm leading-relaxed text-surface-300">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    (() => {
        const items = Array.from(document.querySelectorAll('[data-faq-item]'));
        if (!items.length) return;

        const setOpen = (targetIndex) => {
            items.forEach((item, index) => {
                const panel = item.querySelector('[data-faq-panel]');
                const up = item.querySelector('[data-caret-up]');
                const down = item.querySelector('[data-caret-down]');
                const button = item.querySelector('[data-faq-toggle]');
                const isOpen = index === targetIndex;

                if (panel) panel.classList.toggle('hidden', !isOpen);
                if (up) up.classList.toggle('hidden', !isOpen);
                if (down) down.classList.toggle('hidden', isOpen);
                if (button) button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        };

        items.forEach((item, index) => {
            const button = item.querySelector('[data-faq-toggle]');
            if (!button) return;
            button.addEventListener('click', () => {
                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                setOpen(isExpanded ? -1 : index);
            });
        });
    })();
</script>
@endsection

