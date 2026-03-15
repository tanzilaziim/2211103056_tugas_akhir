<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActualDataController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LstmRunController;
use App\Http\Controllers\Admin\PredictionController;
use App\Http\Controllers\PublicActualDataController;
use App\Http\Controllers\PublicPredictionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::view('/', 'public.home.index')->name('public.home');
Route::view('/prediksi', 'public.prediction.index')->name('public.prediction');
Route::view('/data-aktual', 'public.actual-data.index')->name('public.actual-data');
Route::view('/tentang', 'public.about.index')->name('public.about');
Route::get('/api/public/actual-data', PublicActualDataController::class)->name('public.api.actual-data');
Route::get('/api/public/prediction/meta', [PublicPredictionController::class, 'meta'])->name('public.api.prediction.meta');
Route::get('/api/public/prediction', [PublicPredictionController::class, 'data'])->name('public.api.prediction.data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', fn () => view('admin.auth.login'))->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::view('/data-aktual', 'admin.actual-data.index')->name('actual-data');
        Route::view('/lstm', 'admin.lstm.index')->name('lstm');
        Route::get('/prediksi', fn () => Redirect::route('admin.prediction.data'))->name('prediction');
        Route::get('/prediksi/data', fn () => view('admin.prediction.index', ['section' => 'data']))->name('prediction.data');
        Route::get('/prediksi/grafik', fn () => view('admin.prediction.index', ['section' => 'chart']))->name('prediction.chart');
        Route::view('/pengaturan-akun', 'admin.account.index')->name('account');

        Route::prefix('/api/accounts')->name('api.accounts.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::post('/', [AccountController::class, 'store'])->name('store');
            Route::put('/{user}', [AccountController::class, 'update'])->name('update');
            Route::delete('/{user}', [AccountController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/reset-password', [AccountController::class, 'resetPassword'])->name('reset-password');
        });

        Route::prefix('/api/actual-data')->name('api.actual-data.')->group(function () {
            Route::get('/', [ActualDataController::class, 'index'])->name('index');
            Route::post('/import', [ActualDataController::class, 'import'])->name('import');
        });

        Route::prefix('/api/lstm-runs')->name('api.lstm-runs.')->group(function () {
            Route::get('/', [LstmRunController::class, 'index'])->name('index');
            Route::post('/', [LstmRunController::class, 'store'])->name('store');
            Route::get('/{lstmRun}', [LstmRunController::class, 'show'])->name('show');
            Route::post('/{lstmRun}/stop', [LstmRunController::class, 'stop'])->name('stop');
            Route::post('/{lstmRun}/activate', [LstmRunController::class, 'activate'])->name('activate');
            Route::delete('/{lstmRun}', [LstmRunController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('/api/prediction')->name('api.prediction.')->group(function () {
            Route::get('/meta', [PredictionController::class, 'meta'])->name('meta');
            Route::get('/data', [PredictionController::class, 'data'])->name('data');
            Route::get('/chart', [PredictionController::class, 'chart'])->name('chart');
        });
    });
});

