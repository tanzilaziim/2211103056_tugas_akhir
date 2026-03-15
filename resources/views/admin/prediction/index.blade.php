@php
    $section = $section ?? 'data';
    $isChart = $section === 'chart';
@endphp

@extends('layouts.admin')

@section('title', $isChart ? 'Prediksi Grafik Admin' : 'Prediksi Data Admin')
@section('admin_page_title', $isChart ? 'Prediksi - Grafik' : 'Prediksi - Data')

@section('content')
    @if ($isChart)
        @include('admin.prediction.partials.chart-section')
    @else
        @include('admin.prediction.partials.data-section')
    @endif
@endsection

