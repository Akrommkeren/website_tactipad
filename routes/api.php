<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TactipadController;

Route::prefix('tactipad')->group(function () {
    Route::get('/status', [TactipadController::class, 'getStatus']);
    Route::post('/event', [TactipadController::class, 'pushEvent']);
    Route::post('/reset', [TactipadController::class, 'resetDevice']);
    Route::post('/set-dots', [TactipadController::class, 'setDots']);
    Route::get('/stream', [TactipadController::class, 'streamEvents']);
    Route::get('/history', [TactipadController::class, 'getHistory']);
    Route::delete('/history', [TactipadController::class, 'clearHistory']);
    Route::get('/arduino-sketch', [TactipadController::class, 'downloadArduinoSketch']);
});
