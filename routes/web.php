<?php

use App\Http\Controllers\TactipadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TactipadController::class, 'index'])->name('tactipad.home');
Route::get('/kamus', [TactipadController::class, 'kamus'])->name('tactipad.kamus');
Route::get('/latihan', [TactipadController::class, 'latihan'])->name('tactipad.latihan');
Route::get('/pengaturan', [TactipadController::class, 'pengaturan'])->name('tactipad.pengaturan');

Route::get('/arduino-sketch', [TactipadController::class, 'downloadArduinoSketch'])->name('tactipad.sketch');
Route::post('/event', [TactipadController::class, 'pushEvent'])->name('tactipad.event');
Route::post('/reset', [TactipadController::class, 'resetDevice'])->name('tactipad.reset');
Route::get('/history', [TactipadController::class, 'getHistory'])->name('tactipad.history');
Route::delete('/history', [TactipadController::class, 'clearHistory'])->name('tactipad.history.clear');
