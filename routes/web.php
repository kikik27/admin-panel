<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/products/{filename}', [ImageController::class, 'streamImage']);