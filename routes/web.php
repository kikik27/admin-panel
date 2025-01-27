<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/products/{filename}', [ImageController::class, 'streamImage']);

Route::get('/print', function () {
    $pdf = Pdf::loadView('invoice');
    return $pdf->stream();

});