<?php

use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::prefix('post')->name('post.')->group(function () {
//         Route::get('/', [PostsController::class, 'index'])->name('index');
//     });
// });
Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostsController::class, 'create'])->name('posts.create');
Route::post('/posts/store', [PostsController::class, 'store'])->name('posts.store');
Route::get('/posts/edit/{id}', [PostsController::class, 'edit'])->name('posts.edit');
Route::post('/posts/update/{id}', [PostsController::class, 'update'])->name('posts.update');
Route::get('/posts/destroy/{id}', [PostsController::class, 'destroy'])->name('posts.destroy');
Route::delete('/posts/delete-multiple', [PostsController::class, 'deleteMultiple'])->name('posts.deleteMultiple');
Route::get('/posts/detail/{slug}', [PostsController::class, 'detail'])->name('posts.detail');

//CKEditor Routes
Route::post('/Ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
Route::post('/Ckeditor/store', [CkeditorController::class, 'store'])->name('ckeditor.store');
Route::post('/ckeditor/delete-images', [CkeditorController::class, 'deleteImages'])->name('ckeditor.deleteImages');