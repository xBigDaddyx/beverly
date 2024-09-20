<?php

use Illuminate\Support\Facades\Route;
use Xbigdaddyx\Beverly\Controller\CartonNumberController;
use Xbigdaddyx\Beverly\Controller\PurchaseOrderController;

Route::get('beverly/carton/number', CartonNumberController::class)->name('filament.beverly.api.carton-number');
Route::get('beverly/carton/po', PurchaseOrderController::class)->name('filament.beverly.api.carton-po');
