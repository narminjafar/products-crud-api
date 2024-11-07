<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth'], function () {

        Route::resource('products', ProductController::class)->except(['create', 'edit']);

    });


