<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosterListController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\CodeController;

Route::post('/image-upload', [ImageUploadController::class, 'upload'])->name('image.upload');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('login');
})->name('login.page');;

Route::get('/poster_list', [PosterListController::class, 'index']);
Route::get('/preview', [PreViewController::class, 'index'])->name('preview.page');
Route::get('/preview', [PreviewController::class, 'previewPage'])->name('preview.page');
Route::post('/image/delete', [ImageUploadController::class, 'delete'])->name('image.delete');
Route::post('/code/save', [CodeController::class, 'save'])->name('code.save');