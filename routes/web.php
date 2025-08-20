<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController; // Pastikan ini ada
use Illuminate\Support\Facades\Route;

// Mengarahkan halaman utama ('/') ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Mengubah rute default setelah login dari '/dashboard' menjadi '/assets'
Route::get('/dashboard', function () {
    return redirect()->route('assets.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute untuk menampilkan halaman utama aset
Route::get('/assets', [AssetController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute API dan resource lainnya (sudah benar)
    Route::get('/assets/data', [AssetController::class, 'getDashboardData'])->name('assets.data');
    Route::get('/assets/export', [AssetController::class, 'exportCsv'])->name('assets.export');
    Route::resource('assets', AssetController::class)->except(['index']);
});

require __DIR__.'/auth.php';
