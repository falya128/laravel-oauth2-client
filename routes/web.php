<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/auth/redirect', [\App\Http\Controllers\AuthController::class, 'redirect'])->name('login');
Route::get('/auth/callback', [\App\Http\Controllers\AuthController::class, 'callback']);

Route::get('/', fn () => view('app'));

Route::middleware('auth')->group(function () {
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});
