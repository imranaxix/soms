<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopOwnerController;

// Simple redirect to the dashboard
Route::get('/', function () {
    return redirect('/shop/dashboard');
});

// Basic Shop Routes
Route::get('/shop/dashboard', [ShopOwnerController::class, 'dashboard'])->name('shop.dashboard');

// Shop Order Routes
Route::get('/shop/orders', [ShopOwnerController::class, 'orders'])->name('shop.orders.index');
Route::get('/shop/orders/create', [ShopOwnerController::class, 'createOrder'])->name('shop.orders.create');

// Other Shop Pages
Route::get('/shop/manufacturers', [ShopOwnerController::class, 'manufacturers'])->name('shop.manufacturers');
Route::get('/shop/payments', [ShopOwnerController::class, 'payments'])->name('shop.payments');
Route::get('/shop/reports', [ShopOwnerController::class, 'reports'])->name('shop.reports');