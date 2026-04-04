<header class="w-full" id="public-header">
    <div class="flex items-center justify-between bg-surface-50 px-4 py-1 md:px-8">
        <span id="header-date" class="text-xs font-normal uppercase tracking-wide text-surface-300 md:text-sm"></span>
        <div class="flex items-center gap-2">
            <span class="text-xs font-normal uppercase text-surface-300 md:text-sm">STANDAR WAKTU INDONESIA</span>
            <span id="header-time" class="text-xs font-normal tabular-nums text-primary-300 md:text-sm"></span>
        </div>
    </div>

    <div class="border-b-2 border-surface-200 bg-white px-4 py-5 md:px-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex shrink-0 items-center gap-3">
                <span
                    class="select-none text-[32px] font-extrabold leading-none tracking-tight md:text-[42px]"
                    style="font-family:'Montserrat',sans-serif;"
                    aria-label="SiPKUI"
                >
                    <span style="color:#0B847A;">SiP</span><span style="color:#59C5BF;">KUI</span>
                </span>
                <div class="hidden sm:block">
                    <p class="text-sm font-bold leading-tight text-surface-400 md:text-base">SISTEM PREDIKSI KUALITAS UDARA</p>
                    <p class="text-sm font-bold leading-tight text-surface-400 md:text-base">KABUPATEN INDRAMAYU</p>
                </div>
            </a>

            <nav class="hidden items-center gap-8 lg:flex">
                <a href="{{ route('public.home') }}" class="relative pb-2 text-lg font-bold leading-none {{ request()->routeIs('public.home') ? 'text-primary-300' : 'text-surface-300' }}">Beranda @if (request()->routeIs('public.home'))<span class="absolute bottom-0 left-0 h-1 w-full rounded-full bg-primary-300"></span>@endif</a>
                <a href="{{ route('public.prediction') }}" class="relative pb-2 text-lg font-bold leading-none {{ request()->routeIs('public.prediction') ? 'text-primary-300' : 'text-surface-300' }}">Prediksi @if (request()->routeIs('public.prediction'))<span class="absolute bottom-0 left-0 h-1 w-full rounded-full bg-primary-300"></span>@endif</a>
                <a href="{{ route('public.actual-data') }}" class="relative pb-2 text-lg font-bold leading-none {{ request()->routeIs('public.actual-data') ? 'text-primary-300' : 'text-surface-300' }}">Data Aktual @if (request()->routeIs('public.actual-data'))<span class="absolute bottom-0 left-0 h-1 w-full rounded-full bg-primary-300"></span>@endif</a>
                <a href="{{ route('public.about') }}" class="relative pb-2 text-lg font-bold leading-none {{ request()->routeIs('public.about') ? 'text-primary-300' : 'text-surface-300' }}">Tentang @if (request()->routeIs('public.about'))<span class="absolute bottom-0 left-0 h-1 w-full rounded-full bg-primary-300"></span>@endif</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="https://wa.me/6281223967559" target="_blank" rel="noopener noreferrer" class="hidden items-center gap-2 rounded-full border border-surface-300 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-surface-50 md:flex">
                    <i class="ph ph-phone"></i>
                    <span>PUSAT BANTUAN</span>
                </a>

                <button id="mobile-menu-btn" class="p-2 text-surface-300 lg:hidden" aria-label="Toggle menu">
                    <i id="mobile-menu-open" class="ph ph-list text-2xl"></i>
                    <i id="mobile-menu-close" class="ph ph-x hidden text-2xl"></i>
                </button>
            </div>
        </div>

        <div class="mt-2 sm:hidden">
            <p class="text-sm font-bold leading-tight text-surface-400">SISTEM PREDIKSI KUALITAS UDARA KABUPATEN INDRAMAYU</p>
        </div>

        <nav id="mobile-menu" class="mt-4 hidden flex-col gap-4 border-t border-surface-200 pt-4 lg:hidden">
            <a href="{{ route('public.home') }}" class="py-1 text-base font-bold {{ request()->routeIs('public.home') ? 'text-primary-300' : 'text-surface-300' }}">Beranda</a>
            <a href="{{ route('public.prediction') }}" class="py-1 text-base font-bold {{ request()->routeIs('public.prediction') ? 'text-primary-300' : 'text-surface-300' }}">Prediksi</a>
            <a href="{{ route('public.actual-data') }}" class="py-1 text-base font-bold {{ request()->routeIs('public.actual-data') ? 'text-primary-300' : 'text-surface-300' }}">Data Aktual</a>
            <a href="{{ route('public.about') }}" class="py-1 text-base font-bold {{ request()->routeIs('public.about') ? 'text-primary-300' : 'text-surface-300' }}">Tentang</a>
            <a href="https://wa.me/6281223967559" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 text-sm text-surface-300">
                <i class="ph ph-phone"></i>
                <span>PUSAT BANTUAN</span>
            </a>
        </nav>
    </div>
</header>

<script>
    (() => {
        const DAYS = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
        const MONTHS = ["JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"];

        const dateEl = document.getElementById('header-date');
        const timeEl = document.getElementById('header-time');

        const updateClock = () => {
            const now = new Date();
            if (dateEl) {
                dateEl.textContent = `${DAYS[now.getDay()]}, ${now.getDate()} ${MONTHS[now.getMonth()]} ${now.getFullYear()}`;
            }
            if (timeEl) {
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                timeEl.textContent = `${hh}:${mm}:${ss} WIB`;
            }
        };

        updateClock();
        setInterval(updateClock, 1000);

        const button = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('mobile-menu-open');
        const closeIcon = document.getElementById('mobile-menu-close');

        if (button && menu && openIcon && closeIcon) {
            button.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                menu.classList.toggle('flex');
                openIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    })();
</script>
