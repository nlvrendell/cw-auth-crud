<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/test', function () {
    return Inertia::render('Test', [

    ]);
});

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/dashboard', [\App\Http\Controllers\CW\DomainController::class, 'index'])->name('dashboard');
Route::get('/users', [\App\Http\Controllers\CW\UserController::class, 'index'])->name('users');
