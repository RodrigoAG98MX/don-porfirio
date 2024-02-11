<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlatilloController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
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

/*
 * Lic. Rodrigo Aguilar García - 2024
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

Route::prefix('panel')->name('panel.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('sucursales', SucursalController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('platillos', PlatilloController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ventas', VentaController::class)->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__ . '/auth.php';
