@extends('layouts.admin-auth')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(420px,585px)]">
    <div class="relative min-h-[44vh] overflow-hidden lg:min-h-screen">
        <img
            src="{{ Vite::asset('resources/assets/backgrounds/bg-login.jpg') }}"
            alt="Pemandangan industri Kabupaten Indramayu"
            class="absolute inset-0 h-full w-full object-cover"
        >
        <div class="absolute inset-0 bg-black/35"></div>

        <div class="relative z-10 flex min-h-[44vh] items-center justify-center px-6 pb-28 pt-12 text-center sm:pb-24 lg:min-h-screen lg:px-10 lg:pb-24">
            <div>
                <h1 class="text-xl font-bold leading-tight tracking-wide text-surface-50 sm:text-2xl lg:text-[32px]">
                    SISTEM INFORMASI PREDIKSI KUALITAS UDARA
                </h1>
                <h2 class="mt-2 text-xl font-bold leading-tight tracking-wide text-surface-50 sm:text-2xl lg:mt-3 lg:text-[32px]">
                    KABUPATEN INDRAMAYU
                </h2>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 z-10 px-4 pb-4 pt-3 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-[11px] text-primary-100 sm:text-sm lg:text-base">
                <span class="inline-flex items-center gap-1 text-center font-bold">
                    <i class="ph ph-copyright"></i>
                    <span>2026 - Dinas Lingkungan Hidup Kabupaten Indramayu</span>
                </span>
                <img
                    src="{{ Vite::asset('resources/assets/images/logo_dlh.png') }}"
                    alt="Logo Dinas Lingkungan Hidup"
                    class="h-5 w-auto object-contain sm:h-6"
                >
                <span class="hidden font-bold sm:inline">-</span>
                <span class="font-medium">In Collaboration With</span>
                <img
                    src="{{ Vite::asset('resources/assets/images/logo_tup_horizontal_white.png') }}"
                    alt="Telkom University Purwokerto"
                    class="h-8 w-auto object-contain sm:h-10"
                >
            </div>
        </div>
    </div>

    <div class="flex min-h-[56vh] items-center justify-center bg-surface-50 px-4 py-8 sm:px-6 sm:py-10 lg:min-h-screen lg:border-l lg:border-surface-200 lg:px-8 lg:py-12 lg:shadow-sm">
        <div class="w-full max-w-[447px] rounded-2xl bg-surface-50 p-1">
            <div class="mb-6 sm:mb-8">
                <h2 class="mb-2 text-[22px] font-semibold text-surface-400">Masuk</h2>
                <p class="text-sm leading-snug text-surface-300 sm:text-base">Selamat datang kembali! Tolong masuk ke akun Anda.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-danger-300 bg-danger-50 px-4 py-3 text-sm text-danger-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="flex flex-col gap-5 sm:gap-6">
                @csrf
                <div class="flex flex-col gap-2">
                    <label for="username" class="text-sm text-surface-300 sm:text-base">
                        Username <span class="font-normal text-danger-300">*</span>
                    </label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        required
                        class="h-[50px] w-full rounded-[15px] border border-surface-300/50 bg-white px-4 text-base text-surface-400 outline-none transition-all focus:border-primary-300 focus:ring-2 focus:ring-primary-300/15"
                    >
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm text-surface-300 sm:text-base">Kata sandi</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="h-[50px] w-full rounded-[15px] border border-surface-300/50 bg-white px-4 text-base text-surface-400 outline-none transition-all focus:border-primary-300 focus:ring-2 focus:ring-primary-300/20"
                    >
                </div>

                <div class="flex justify-end">
                    <span class="text-sm text-surface-300 sm:text-base">Reset kata sandi melalui Super Admin</span>
                </div>

                <button
                    type="submit"
                    class="h-[50px] w-full rounded-[15px] bg-primary-300 text-base font-bold tracking-[0.18em] text-surface-50 transition-colors hover:bg-primary-400"
                >
                    MASUK
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

