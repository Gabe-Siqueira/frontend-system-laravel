<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'autentication'])->name('login-autentication');

Route::middleware(['front'])->group(function () {
    // LoginController
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // HomeController
    Route::get('/home', [HomeController::class, 'index'])->name('home');

});
