<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('public.home');
Route::view('/prediksi', 'public.prediction')->name('public.prediction');
Route::view('/data-aktual', 'public.actual-data')->name('public.actual-data');
Route::view('/tentang', 'public.about')->name('public.about');
