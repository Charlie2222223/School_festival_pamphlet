<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosterListController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\AdminController;

Route::post('/image-upload', [ImageUploadController::class, 'upload'])->name('image.upload');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('login');
})->name('login.page');;

Route::get('/poster', [AuthController::class, 'show_poster'])->name('poster');

Route::get('/poster_list', [PosterListController::class, 'index'])->name('poster.page');;;
Route::get('/preview', [PreViewController::class, 'index'])->name('preview.page');
Route::get('/preview', [PreviewController::class, 'previewPage'])->name('preview.page');
Route::post('/image/delete', [ImageUploadController::class, 'delete'])->name('image.delete');
Route::post('/code/save', [CodeController::class, 'save'])->name('code.save');
Route::get('/poster_admin', [AdminController::class, 'index'])->name('poster_admin');
Route::get('/admin_show', [AdminController::class, 'admin_show'])->name('admin_show');
Route::get('/admin_edit', [AdminController::class, 'admin_edit'])->name('admin_edit');

Route::get('/get-class-code/{id}', [AdminController::class, 'getClassCode']);
Route::get('/code-history/{classId}', [CodeController::class, 'getHistory']);

Route::get('/admin_classes', [AdminController::class, 'admin_classes'])->name('admin_user');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');