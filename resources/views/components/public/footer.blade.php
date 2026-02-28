<footer class="bg-primary-300 text-white">
    <div class="mx-auto max-w-screen-xl px-6 pb-0 pt-10 md:px-10">
        <div class="flex flex-col gap-6 pb-10 md:flex-row md:items-start md:gap-12">
            <div class="flex shrink-0 items-start gap-4">
                <img
                    src="{{ Vite::asset('resources/assets/images/logo_bplh_outline.png') }}"
                    alt="Logo DLH Kabupaten Indramayu"
                    class="h-16 w-16 shrink-0 object-contain"
                />
                <p class="max-w-xs whitespace-pre-line text-base font-bold leading-snug text-surface-50">DINAS LINGKUNGAN HIDUP /
BADAN PENGENDALIAN LINGKUNGAN HIDUP
KABUPATEN INDRAMAYU</p>
            </div>

            <div class="flex-1"></div>

            <div>
                <p class="mb-4 text-base font-bold text-primary-100">MEDIA SOSIAL</p>
                <div class="flex items-center gap-5 text-3xl">
                    <a href="#" aria-label="Instagram" class="transition-opacity hover:opacity-80">
                        <img src="{{ Vite::asset('resources/assets/icons/instagram.svg') }}" alt="Instagram" class="h-7 w-7 object-contain">
                    </a>
                    <a href="#" aria-label="YouTube" class="transition-opacity hover:opacity-80">
                        <img src="{{ Vite::asset('resources/assets/icons/youtube.svg') }}" alt="YouTube" class="h-7 w-7 object-contain">
                    </a>
                    <a href="#" aria-label="Facebook" class="transition-opacity hover:opacity-80">
                        <img src="{{ Vite::asset('resources/assets/icons/facebook.svg') }}" alt="Facebook" class="h-7 w-7 object-contain">
                    </a>
                    <a href="#" aria-label="TikTok" class="transition-opacity hover:opacity-80">
                        <img src="{{ Vite::asset('resources/assets/icons/tiktok.svg') }}" alt="TikTok" class="h-7 w-7 object-contain">
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 pb-10 md:grid-cols-3">
            <div>
                <p class="mb-5 text-base font-bold text-primary-100">KONTAK KAMI</p>
                <div class="flex flex-col gap-4 text-sm font-bold text-surface-50">
                    <div class="flex items-start gap-3"><i class="ph ph-map-pin mt-0.5 text-xl"></i><span>Jl. Mayor Dasuki, Penganjang, Kec. Sindang,<br>Kabupaten Indramayu, 45221</span></div>
                    <div class="flex items-center gap-3"><i class="ph ph-phone text-xl"></i><span>0812-1267-8721</span></div>
                    <div class="flex items-center gap-3"><i class="ph ph-envelope text-xl"></i><span>dlhindramayu@gmail.com</span></div>
                </div>
            </div>

            <div>
                <p class="mb-5 text-base font-bold text-primary-100">DUKUNGAN</p>
                <div class="flex flex-col gap-4">
                    @include('components.public.link-item', ['label' => 'Peraturan UU Indeks Pencemaran Udara', 'href' => '#'])
                    @include('components.public.link-item', ['label' => 'Lorem Ipsum Dolor', 'href' => '#'])
                    @include('components.public.link-item', ['label' => 'Lorem Ipsum Dolor', 'href' => '#'])
                </div>
            </div>

            <div>
                <p class="mb-5 text-base font-bold text-primary-100">TAUTAN</p>
                <div class="flex flex-col gap-4">
                    @include('components.public.link-item', ['label' => 'Website DLH Kabupaten Indramayu', 'href' => '#'])
                    @include('components.public.link-item', ['label' => 'Indeks Standar Pencemaran Udara', 'href' => '#'])
                    @include('components.public.link-item', ['label' => 'Lorem Ipsum Dolor', 'href' => '#'])
                </div>
            </div>
        </div>

        <div class="h-px bg-primary-50 opacity-50"></div>

        <div class="flex flex-col items-center justify-center gap-4 py-6 text-sm text-primary-100 sm:flex-row sm:gap-6">
            <span class="inline-flex items-center gap-1 text-center font-bold">
                <i class="ph ph-copyright"></i>
                <span>2026 - Dinas Lingkungan Hidup Kabupaten Indramayu</span>
            </span>
            <span class="hidden font-bold sm:inline">-</span>
            <span class="font-medium">In Collaboration With</span>
            <img
                src="{{ Vite::asset('resources/assets/images/logo_tup_horizontal_white.png') }}"
                alt="Telkom University Purwokerto"
                class="h-10 object-contain"
            />
        </div>
    </div>
</footer>
