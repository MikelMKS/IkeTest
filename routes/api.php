<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\HistoryController;

Route::controller(AuthController::class)->group(function () {
    Route::get('login','login')->name('login');
    Route::get('get_token','getToken')->name('get_token');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(HistoryController::class)->group(function () {
        Route::get('history','index')->name('history');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('users','index')->name('users');
        Route::post('create_user','create')->name('create_user');
        Route::post('update_user','update')->name('update_user');
        Route::delete('delete_user','delete')->name('delete_user');
    });

    Route::controller(CardController::class)->group(function () {
        Route::get('cards','index')->name('cards');
        Route::post('createCard','create')->name('createCard');
    });

});

