<?php

use App\Mail\PasswordChange;
use Illuminate\Support\Facades\Mail;
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

Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
Route::get('/reset', function () {
    return view('auth.passwords.reset');
})->name('password.reset-password');
Auth::routes();
Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
Route::post('custom-login', [\App\Http\Controllers\Auth\LoginController::class, 'customLogin'])->name('login.custom');
Route::post('reset-password', [\App\Http\Controllers\Auth\LoginController::class, 'resetPassword'])->name('login.reset-password');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/upload', [App\Http\Controllers\UserController::class, 'upload'])->name('users.upload')->middleware('auth');;
