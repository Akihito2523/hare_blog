<?php

use App\Http\Controllers\PostController;
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

// アクセスしたページ
Route::get('/', [PostController::class, 'index'])
    ->name('root');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// 認証していなければアクセスできないように制御
Route::resource('posts', PostController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

//認証していなくてもアクセスできる
Route::resource('posts', PostController::class)
    ->only(['show', 'index']);

require __DIR__ . '/auth.php';
