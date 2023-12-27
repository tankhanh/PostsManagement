<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
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

// Posts
Route::get('/posts', [PostsController::class, 'index'])->name('posts.index')->middleware('check_login');
Route::get('/posts/create', [PostsController::class, 'create'])->name('posts.create')->middleware('check_login','check_post_access');
Route::post('/posts/store', [PostsController::class, 'store'])->name('posts.store')->middleware('check_login');
Route::get('/posts/edit/{id}', [PostsController::class, 'edit'])->name('posts.edit')->middleware('check_login', 'check_post_access');
Route::post('/posts/update/{id}', [PostsController::class, 'update'])->name('posts.update')->middleware('check_login', 'check_post_access');
Route::delete('/posts/destroy/{id}', [PostsController::class, 'destroy'])->name('posts.destroy')->middleware('check_login','check_post_access');
Route::delete('/posts/delete-multiple', [PostsController::class, 'deleteMultiple'])->name('posts.deleteMultiple')->middleware('check_login','check_post_access');
Route::get('/posts/detail/{slug}', [PostsController::class, 'detail'])->name('posts.detail')->middleware('check_login');
Route::put('/posts/update-status', [PostsController::class, 'updatePostStatus'])->name('posts.updatePostStatus')->middleware('check_login');

// Caterories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('check_login');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('check_login','check_category_access');
Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store')->middleware('check_login');
Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('check_login','check_category_access');
Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update')->middleware('check_login');
Route::delete('/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('check_login','check_category_access');
Route::delete('/categories/delete-multiple', [CategoryController::class, 'deleteMultiple'])->name('categories.deleteMultiple')->middleware('check_login','check_category_access');
Route::get('/categories/detail/{slug}', [CategoryController::class, 'detail'])->name('categories.detail')->middleware('check_login');
Route::put('/categories/update-status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus')->middleware('check_login');
//CKEditor Routes
Route::post('/Ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
Route::post('/Ckeditor/store', [CkeditorController::class, 'store'])->name('ckeditor.store');
Route::post('/ckeditor/deleteImages', [CkeditorController::class, 'deleteImages'])->name('ckeditor.deleteImages');

// Auth
Route::get('auth/login', [LoginController::class, 'showLogin'])->name('showLogin');
Route::post('auth/login', [LoginController::class, 'login'])->name('login');
Route::get('auth/register', [RegisterController::class, 'showRegister'])->name('showRegister');
Route::post('auth/register', [RegisterController::class, 'register'])->name('register');
Route::get('auth/logout', LogoutController::class)->name('logout');

//Route EditProfile
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('check_login');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('update')->middleware('check_login');