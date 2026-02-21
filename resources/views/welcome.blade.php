<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Color Palette Test | {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-400 px-5 py-10 font-sans text-surface-50 md:px-10">
    <main class="mx-auto w-full max-w-7xl rounded-3xl border border-surface-300/40 bg-[#0b0f16] p-6 md:p-10">
        <header class="mb-10">
            <h1 class="text-3xl font-bold text-surface-50 md:text-4xl">Colors</h1>
            <p class="mt-2 text-surface-200">Preview palet warna untuk website tugas akhir.</p>
        </header>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Primary</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-primary-50 p-4 text-slate-900"><p class="font-semibold">primary-50</p><p>#F0FDFA</p></article>
                <article class="rounded-xl bg-primary-100 p-4 text-slate-900"><p class="font-semibold">primary-100</p><p>#CCFBF1</p></article>
                <article class="rounded-xl bg-primary-200 p-4 text-slate-900"><p class="font-semibold">primary-200</p><p>#5EEAD4</p></article>
                <article class="rounded-xl bg-primary-300 p-4 text-white"><p class="font-semibold">primary-300</p><p>#0F766E</p></article>
                <article class="rounded-xl bg-primary-400 p-4 text-white"><p class="font-semibold">primary-400</p><p>#115E59</p></article>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Secondary</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-secondary-50 p-4 text-slate-900"><p class="font-semibold">secondary-50</p><p>#E0F2FE</p></article>
                <article class="rounded-xl bg-secondary-100 p-4 text-slate-900"><p class="font-semibold">secondary-100</p><p>#BAE6FD</p></article>
                <article class="rounded-xl bg-secondary-200 p-4 text-slate-900"><p class="font-semibold">secondary-200</p><p>#38BDF8</p></article>
                <article class="rounded-xl bg-secondary-300 p-4 text-white"><p class="font-semibold">secondary-300</p><p>#0284C7</p></article>
                <article class="rounded-xl bg-secondary-400 p-4 text-white"><p class="font-semibold">secondary-400</p><p>#075985</p></article>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Surface</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-surface-50 p-4 text-slate-900"><p class="font-semibold">surface-50</p><p>#F8FAFC</p></article>
                <article class="rounded-xl bg-surface-100 p-4 text-slate-900"><p class="font-semibold">surface-100</p><p>#FFFFFF</p></article>
                <article class="rounded-xl bg-surface-200 p-4 text-slate-900"><p class="font-semibold">surface-200</p><p>#E2E8F0</p></article>
                <article class="rounded-xl bg-surface-300 p-4 text-white"><p class="font-semibold">surface-300</p><p>#475569</p></article>
                <article class="rounded-xl bg-surface-400 p-4 text-white"><p class="font-semibold">surface-400</p><p>#0F172A</p></article>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Danger</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-danger-50 p-4 text-slate-900"><p class="font-semibold">danger-50</p><p>#FEE2E2</p></article>
                <article class="rounded-xl bg-danger-100 p-4 text-slate-900"><p class="font-semibold">danger-100</p><p>#FCA5A5</p></article>
                <article class="rounded-xl bg-danger-200 p-4 text-slate-900"><p class="font-semibold">danger-200</p><p>#F87171</p></article>
                <article class="rounded-xl bg-danger-300 p-4 text-white"><p class="font-semibold">danger-300</p><p>#DC2626</p></article>
                <article class="rounded-xl bg-danger-400 p-4 text-white"><p class="font-semibold">danger-400</p><p>#991B1B</p></article>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Success</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-success-50 p-4 text-slate-900"><p class="font-semibold">success-50</p><p>#DCFCE7</p></article>
                <article class="rounded-xl bg-success-100 p-4 text-slate-900"><p class="font-semibold">success-100</p><p>#86EFAC</p></article>
                <article class="rounded-xl bg-success-200 p-4 text-slate-900"><p class="font-semibold">success-200</p><p>#4ADE80</p></article>
                <article class="rounded-xl bg-success-300 p-4 text-white"><p class="font-semibold">success-300</p><p>#16A34A</p></article>
                <article class="rounded-xl bg-success-400 p-4 text-white"><p class="font-semibold">success-400</p><p>#166534</p></article>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Warning</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-warning-50 p-4 text-slate-900"><p class="font-semibold">warning-50</p><p>#FEF3C7</p></article>
                <article class="rounded-xl bg-warning-100 p-4 text-slate-900"><p class="font-semibold">warning-100</p><p>#FDE68A</p></article>
                <article class="rounded-xl bg-warning-200 p-4 text-slate-900"><p class="font-semibold">warning-200</p><p>#FBBF24</p></article>
                <article class="rounded-xl bg-warning-300 p-4 text-slate-900"><p class="font-semibold">warning-300</p><p>#F59E0B</p></article>
                <article class="rounded-xl bg-warning-400 p-4 text-white"><p class="font-semibold">warning-400</p><p>#B45309</p></article>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-semibold">ISPU Indicator</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <article class="rounded-xl bg-ispu-baik p-4 text-white"><p class="font-semibold">baik</p><p>#16A34A</p></article>
                <article class="rounded-xl bg-ispu-sedang p-4 text-white"><p class="font-semibold">sedang</p><p>#2563EB</p></article>
                <article class="rounded-xl bg-ispu-tidak-sehat p-4 text-slate-900"><p class="font-semibold">tidak-sehat</p><p>#FACC15</p></article>
                <article class="rounded-xl bg-ispu-sangat-tidak-sehat p-4 text-white"><p class="font-semibold">sangat-tidak-sehat</p><p>#DC2626</p></article>
                <article class="rounded-xl bg-ispu-berbahaya p-4 text-white"><p class="font-semibold">berbahaya</p><p>#111827</p></article>
            </div>
        </section>
    </main>
</body>
</html>
