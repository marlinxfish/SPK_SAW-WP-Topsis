<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\SAWController;
use App\Http\Controllers\WPController;
use App\Http\Controllers\TOPSISController;

// Route Home
Route::get('/', function () {
    return redirect()->route('kriteria.index');
})->name('home');

// Route Welcome
Route::get('/welcome', function () {
    return view('welcome');
});

// Route Kriteria
Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
Route::post('/kriteria/store', [KriteriaController::class, 'store'])->name('kriteria.store');
Route::put('/kriteria/update/{id}', [KriteriaController::class, 'update'])->name('kriteria.update');
Route::delete('/kriteria/delete/{id}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

// Route Alternatif
Route::get('/alternatif', [AlternatifController::class, 'index'])->name('alternatif.index');
Route::post('/alternatif/store', [AlternatifController::class, 'store'])->name('alternatif.store');
Route::put('/alternatif/update/{id}', [AlternatifController::class, 'update'])->name('alternatif.update');
Route::delete('/alternatif/delete/{id}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy');


// Route Penilaian
Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
Route::post('/penilaian/store', [PenilaianController::class, 'store'])->name('penilaian.store');
Route::get('/penilaian/get-nilai/{alternatifId}/{kriteriaId}', [PenilaianController::class, 'getNilai'])->name('penilaian.get');

// Route SAW
Route::get('/saw', [SAWController::class, 'index'])->name('saw.index');

// Route WP
Route::get('/wp', [WPController::class, 'index'])->name('wp.index');

// Route TOPSIS
Route::get('/topsis', [TOPSISController::class, 'index'])->name('topsis.index');