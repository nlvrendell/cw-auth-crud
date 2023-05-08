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
    return Inertia::render('Test', []);
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [\App\Http\Controllers\Auth\AuthenticationController::class, 'index'])->name('login');

Route::group(['middleware' => ['cw_valid']], function () {

    Route::get('/test-middleware', function () {
        return Inertia::render('Test', []);
    });

    Route::get('/profile', [\App\Http\Controllers\CW\ProfileController::class, 'index'])->name('profile');

    Route::get('/dashboard', [\App\Http\Controllers\CW\DomainController::class, 'index'])->name('dashboard');
    Route::resource('domain', \App\Http\Controllers\CW\DomainController::class)->only(['store', 'update', 'destroy']);
    // Route::get('/domain/{domain}/users', [\App\Http\Controllers\CW\Domain\UserController::class, 'index'])->name('domain.users');

    Route::get('/users', [\App\Http\Controllers\CW\UserController::class, 'index'])->name('users');
    Route::post('/domain/users', [\App\Http\Controllers\CW\UserController::class, 'store'])->name('domain.users.store');
    Route::resource('domain.users', \App\Http\Controllers\CW\Domain\UserController::class)->only(['index', 'update', 'destroy']);

    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logout'])->name('logout');

    Route::group(['middleware' => ['scope:Reseller']], function () {
        Route::get('/reseller-scope', function () {
            return 'Reseller here';
        });
    });

    Route::group(['middleware' => ['scope:Super User']], function () {
        // Superadmin
        Route::resource('resellers', \App\Http\Controllers\CW\ResellerController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('/super-scope', function () {
            return 'Super user here';
        });
    });
});
