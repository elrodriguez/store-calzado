<?php

use App\Http\Controllers\ClientController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\SaleController;
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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('pettycash', PettyCashController::class);
    Route::resource('providers', ProviderController::class);

    Route::resource('sales', SaleController::class);

    Route::post(
        'search/products',
        [ProductController::class, 'searchProduct']
    )->name('search_product');

    Route::post(
        'show/detail_product',
        [ProductController::class, 'showDetailProduct']
    )->name('show_detail_product');

    Route::post(
        'products/showdetails/{id}',
        [ProductController::class, 'showdetails']
    )->name('showdetails');


    Route::resource('clients', ClientController::class);
});
