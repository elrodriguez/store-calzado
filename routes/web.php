<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LocalSaleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

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

    Route::get(
        'pdf/sales/ticket/{id}',
        [SaleController::class, 'ticketPdf']
    )->name('ticketpdf_sales');


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

    Route::post(
        'products/input',
        [ProductController::class, 'saveInput']
    )->name('input_products');


    Route::resource('clients', ClientController::class);
    Route::resource('users', UserController::class);
    Route::resource('establishments', LocalSaleController::class);


    Route::post(
        'local/series',
        [LocalSaleController::class, 'series']
    )->name('localseriesbyid');

    Route::resource('series', SerieController::class);

    Route::get(
        'reports/list',
        [ReportController::class, 'index']
    )->name('reports');

    Route::get(
        'reports/saleindate',
        [ReportController::class, 'sales_report']
    )->name('sale_report');

    Route::get(
        'reports/inventoryindate',
        [ReportController::class, 'inventory_report']
    )->name('inventory_report');


    Route::get(
        'reports/sales/{begin_date}/{end_date}/{download}',
        [ReportController::class, 'sales_report_export']
    )->name('sales_report_export');

    Route::get(
        'reports/inventory/{download}',
        [ReportController::class, 'inventory_report_export']
    )->name('inventory_report_export');
});
