<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Xbigdaddyx\Beverly\Controller\SearchController;
use Xbigdaddyx\Beverly\Controller\VerificationController;

Route::middleware(['web', 'auth'])->prefix('beverly')->group(function () {

    Route::get('/carton/check', [SearchController::class, 'index'])->name('beverly.check.carton.release');
    Route::get('/{carton}/polybag', [VerificationController::class, 'index'])->name('filament.beverly.validation.polybag.release');
    Route::get('/{carton}/completed', [VerificationController::class, 'completed'])->name('filament.beverly.completed.carton.release');
});
