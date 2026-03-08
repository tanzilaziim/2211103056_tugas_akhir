<?php

use App\Http\Controllers\PublicActualDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::view('/', 'public.home.index')->name('public.home');
Route::view('/prediksi', 'public.prediction.index')->name('public.prediction');
Route::view('/data-aktual', 'public.actual-data.index')->name('public.actual-data');
Route::view('/tentang', 'public.about.index')->name('public.about');
Route::get('/api/public/actual-data', PublicActualDataController::class)->name('public.api.actual-data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn () => view('admin.auth.login'))->name('login');

    // Frontend flow sementara: submit login langsung ke dashboard preview.
    Route::post('/login', function (Request $request) {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        return redirect()->route('admin.dashboard');
    })->name('login.submit');

    Route::view('/dashboard', 'admin.dashboard.index')->name('dashboard');
    Route::view('/data-aktual', 'admin.actual-data.index')->name('actual-data');
    Route::get('/prediksi', fn () => Redirect::route('admin.prediction.data'))->name('prediction');
    Route::view('/prediksi/data', 'admin.prediction.data')->name('prediction.data');
    Route::view('/prediksi/grafik', 'admin.prediction.chart')->name('prediction.chart');
    Route::view('/pengaturan-akun', 'admin.account.index')->name('account');
});

