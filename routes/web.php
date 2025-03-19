<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosterListController;
use App\Http\Controllers\PreviewController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('login');
})->name('login.page');;

Route::get('/poster_list', [PosterListController::class, 'index']);
Route::get('/preview', [PreViewController::class, 'index'])->name('preview.page');