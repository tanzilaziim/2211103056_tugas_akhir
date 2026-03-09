@extends('layouts.admin')
@section('title', 'LSTM Overview Admin')
@section('admin_page_title', 'LSTM - Overview')
@section('content')
<div data-page="admin-lstm-overview" class="min-h-full bg-surface-50">
    <div class="mb-6">
        <h1 class="mb-1 text-3xl font-bold text-surface-400">Proses Prediksi dengan LSTM</h1>
        <p class="text-base text-surface-300">Jalankan proses prediksi dengan menekan satu tombol</p>
    </div>
    <div class="mb-6 inline-flex items-center rounded-full border border-primary-300 bg-surface-100 p-[3px]">
        <button data-lstm-tab="overview" class="rounded-full bg-primary-300 px-8 py-1.5 text-sm text-surface-50 transition-colors sm:px-10">Overview</button>
        <button data-lstm-tab="evaluation" class="rounded-full px-8 py-1.5 text-sm text-surface-300 transition-colors hover:text-primary-300 sm:px-10">Evaluation</button>
        <button data-lstm-tab="log" class="rounded-full px-8 py-1.5 text-sm text-surface-300 transition-colors hover:text-primary-300 sm:px-10">Log</button>
    </div>
    @include('admin.lstm.partials.overview-section')
    @include('admin.lstm.partials.evaluation-section')
    @include('admin.lstm.partials.log-section')
    @include('admin.lstm.partials.delete-modal')
</div>
@endsection
