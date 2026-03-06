<?php

use App\Http\Controllers\PublicActualDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('public.home');
Route::view('/prediksi', 'public.prediction')->name('public.prediction');
Route::view('/data-aktual', 'public.actual-data')->name('public.actual-data');
Route::view('/tentang', 'public.about')->name('public.about');
Route::get('/api/public/actual-data', PublicActualDataController::class)->name('public.api.actual-data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn () => view('admin.login'))->name('login');

    // Frontend flow sementara: submit login langsung ke dashboard preview.
    Route::post('/login', function (Request $request) {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        return redirect()->route('admin.dashboard');
    })->name('login.submit');

    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/data-aktual', 'admin.actual-data')->name('actual-data');
});

