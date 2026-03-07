@extends('layouts.admin')

@section('title', 'Data Aktual Admin')
@section('admin_page_title', 'Data Aktual')

@section('content')
<div data-page="admin-actual-data" class="min-h-full bg-surface-50">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-surface-400">Data Aktual</h1>
        <p class="mt-1 text-base text-surface-300">Unggah data aktual untuk bahan prediksi</p>
    </div>

    <div class="flex flex-col items-start gap-6 xl:flex-row">
        <div class="flex w-full min-w-0 flex-1 flex-col gap-6">
            <div class="rounded-2xl border border-surface-200 bg-surface-100 p-5 shadow-sm">
                <div
                    data-drop-zone
                    class="mb-4 flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-surface-300/50 bg-surface-200/50 px-4 py-8 transition-colors duration-200"
                >
                    <button type="button" data-btn-choose-file class="rounded-full border border-surface-200 bg-surface-100 px-6 py-2 text-sm text-surface-300 transition-colors hover:bg-surface-50">
                        Cari File
                    </button>
                    <p class="text-center text-sm text-surface-300">atau drop file dataset disini</p>
                    <input data-file-input type="file" accept=".csv" class="hidden">
                </div>

                <ul class="mb-4 space-y-1">
                    <li class="flex items-start gap-2 text-sm text-surface-300"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-surface-300"></span>Upload file dataset dalam bentuk csv</li>
                    <li class="flex items-start gap-2 text-sm text-surface-300"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-surface-300"></span>Kolom dataset harus berisi: waktu, pm10, pm2.5</li>
                </ul>

                <p data-file-name class="mb-3 hidden truncate text-xs text-primary-300"></p>
                <p data-file-error class="mb-3 hidden text-xs text-danger-300"></p>

                <div class="flex justify-center">
                    <button type="button" data-btn-upload class="inline-flex items-center gap-2 rounded-full bg-primary-300 px-6 py-2.5 text-sm text-surface-50 transition-colors hover:bg-primary-400">
                        <i class="ph ph-upload-simple text-lg"></i>
                        Unggah File
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-surface-200 bg-surface-100 p-5 shadow-sm">
                <h2 class="mb-3 text-xl font-bold text-surface-400">Grafik PM10</h2>
                <div class="h-[200px] w-full">
                    <canvas id="admin-actual-pm10-chart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-surface-200 bg-surface-100 p-5 shadow-sm">
                <h2 class="mb-3 text-xl font-bold text-surface-400">Grafik PM2.5</h2>
                <div class="h-[200px] w-full">
                    <canvas id="admin-actual-pm25-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="w-full shrink-0 xl:w-[480px]">
            <div class="flex h-full flex-col rounded-2xl border border-surface-200 bg-surface-100 shadow-sm">
                <div class="p-6 pb-4">
                    <h2 class="mb-4 text-xl font-bold text-surface-400">Dataset</h2>

                    <div class="relative">
                        <button
                            type="button"
                            data-date-toggle
                            class="inline-flex items-center gap-2 rounded-full border border-primary-300 px-4 py-2 text-sm text-surface-300 transition-colors hover:bg-primary-50"
                        >
                            <i class="ph-bold ph-calendar-check text-2xl text-primary-300"></i>
                            <span data-date-display></span>
                        </button>

                        <div data-date-menu class="absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-xl border border-surface-200 bg-surface-100 p-3 shadow-lg">
                            <div class="mb-2 flex items-center justify-between">
                                <button type="button" data-date-prev class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                                    <i class="ph ph-caret-left text-base text-surface-300"></i>
                                </button>
                                <span data-date-current class="text-sm font-medium text-surface-300"></span>
                                <button type="button" data-date-next class="rounded-lg p-1 transition-colors hover:bg-surface-200 disabled:cursor-not-allowed disabled:opacity-30">
                                    <i class="ph ph-caret-right text-base text-surface-300"></i>
                                </button>
                            </div>
                            <div data-date-list class="max-h-48 space-y-1 overflow-y-auto"></div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-6 pb-6">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-surface-100">
                            <tr><th colspan="3" class="h-0.5 border-0 bg-primary-300 p-0"></th></tr>
                            <tr>
                                <th class="py-2 text-center font-normal text-surface-300">Waktu</th>
                                <th class="py-2 text-center font-normal text-surface-300">PM10</th>
                                <th class="py-2 text-center font-normal text-surface-300">PM2.5</th>
                            </tr>
                            <tr><th colspan="3" class="h-0.5 border-0 bg-primary-300 p-0"></th></tr>
                        </thead>
                        <tbody data-dataset-body></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
