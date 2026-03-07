@extends('layouts.admin')

@section('title', 'Prediksi Data Admin')
@section('admin_page_title', 'Prediksi - Data')

@section('content')
<div data-page="admin-prediction-data" class="min-h-full bg-surface-50">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-surface-400">Hasil Prediksi</h1>
        <p class="mt-1 text-base text-surface-300">Ringkasan data hasil prediksi</p>
    </div>

    @include('admin.prediction._tabs')

    <div class="rounded-[15px] border border-surface-200 bg-surface-100 shadow-sm">
        <div class="px-7 pb-4 pt-7">
            <h2 class="mb-4 text-2xl font-bold text-surface-400">Data Hasil Prediksi</h2>

            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex rounded-full border border-primary-300 bg-surface-100 p-[3px]">
                    <button type="button" data-range="24 Jam" class="rounded-full bg-primary-300 px-4 py-1.5 text-sm text-surface-50">24 Jam</button>
                    <button type="button" data-range="7 Hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">7 Hari</button>
                    <button type="button" data-range="30 Hari" class="rounded-full px-4 py-1.5 text-sm text-surface-300 hover:text-primary-300">30 Hari</button>
                </div>

                <div class="relative">
                    <button type="button" data-prediction-date-toggle class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50">
                        <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                        <span data-prediction-date-label>01 Januari 2026</span>
                    </button>

                    <div data-prediction-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                        <div data-date-mode="single">
                            <div class="mb-2 flex items-center justify-between">
                                <button type="button" data-prediction-date-prev class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                                    <i class="ph ph-caret-left text-base text-surface-300"></i>
                                </button>
                                <span data-prediction-date-current class="text-sm font-medium text-surface-300"></span>
                                <button type="button" data-prediction-date-next class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                                    <i class="ph ph-caret-right text-base text-surface-300"></i>
                                </button>
                            </div>
                            <div data-prediction-date-list class="max-h-48 space-y-1 overflow-y-auto"></div>
                        </div>

                        <div data-date-mode="range" class="hidden space-y-2">
                            <p class="text-xs font-medium text-surface-300">Pilih rentang 7 hari</p>
                            <input data-prediction-range-start type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                            <input data-prediction-range-end type="date" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                            <button type="button" data-prediction-range-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                        </div>

                        <div data-date-mode="month" class="hidden space-y-2">
                            <p class="text-xs font-medium text-surface-300">Pilih bulan</p>
                            <input data-prediction-month type="month" class="w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-300 outline-none focus:border-primary-300">
                            <button type="button" data-prediction-month-apply class="w-full rounded-lg bg-primary-300 px-3 py-2 text-sm font-medium text-surface-50">Terapkan</button>
                        </div>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 rounded-full border border-primary-300 bg-surface-100 px-4 py-2">
                    <div class="relative">
                        <button type="button" data-prediction-category-toggle class="inline-flex min-w-[110px] items-center justify-between gap-2 text-sm text-surface-300">
                            <span data-prediction-category-label>Semua</span>
                            <i class="ph ph-caret-down text-base text-primary-300"></i>
                        </button>
                        <div data-prediction-category-menu class="absolute right-0 top-full z-20 mt-2 hidden w-36 rounded-xl border border-surface-200 bg-surface-100 p-1 shadow-lg">
                            <button type="button" data-prediction-category-option="Semua" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">Semua</button>
                            <button type="button" data-prediction-category-option="PM10" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">PM10</button>
                            <button type="button" data-prediction-category-option="PM2.5" class="w-full rounded-lg px-3 py-2 text-left text-sm text-surface-300 hover:bg-surface-200">PM2.5</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-7">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-primary-300">
                            <th class="w-1/4 rounded-tl-[15px] px-5 py-4 text-left text-base font-bold text-surface-50">Waktu (Jam)</th>
                            <th class="w-1/4 px-5 py-4 text-left text-base font-bold text-surface-50">PM10</th>
                            <th class="w-1/4 px-5 py-4 text-left text-base font-bold text-surface-50">PM2.5</th>
                            <th class="w-1/4 rounded-tr-[15px] px-5 py-4 text-left text-base font-bold text-surface-50">Indikator Kesehatan</th>
                        </tr>
                    </thead>
                    <tbody data-prediction-table-body></tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-1.5 px-7 py-5" data-prediction-pagination></div>
    </div>
</div>
@endsection
