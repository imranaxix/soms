<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopOwnerController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\AuthController;

// Simple redirect to the login
Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Basic Shop Routes (Protected)
Route::middleware(['auth', 'role:shop_owner'])->group(function () {
    Route::get('/shop/dashboard', [ShopOwnerController::class, 'dashboard'])->name('shop.dashboard');
    
    // Shop Order Routes
    Route::get('/shop/orders', [ShopOwnerController::class, 'orders'])->name('shop.orders.index');
    Route::get('/shop/orders/create', [ShopOwnerController::class, 'createOrder'])->name('shop.orders.create');
    Route::get('/shop/orders/{id}', [ShopOwnerController::class, 'showOrder'])->name('shop.orders.show');
    
    // Other Shop Pages
    Route::get('/shop/manufacturers', [ShopOwnerController::class, 'manufacturers'])->name('shop.manufacturers');
    Route::get('/shop/payments', [ShopOwnerController::class, 'payments'])->name('shop.payments');
    Route::get('/shop/reports', [ShopOwnerController::class, 'reports'])->name('shop.reports');
});

// Manufacturer Routes (Protected)
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [ManufacturerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [ManufacturerController::class, 'orders'])->name('orders.index');
    Route::get('/catalog', [ManufacturerController::class, 'catalog'])->name('catalog.index');
    Route::get('/catalog/create', [ManufacturerController::class, 'createProduct'])->name('catalog.create');
    Route::post('/catalog', [ManufacturerController::class, 'storeProduct'])->name('catalog.store');
    Route::delete('/catalog/{id}', [ManufacturerController::class, 'destroyProduct'])->name('catalog.destroy');

    Route::get('/production', [ManufacturerController::class, 'production'])->name('production.index');
    Route::get('/payments', [ManufacturerController::class, 'payments'])->name('payments.index');
    Route::get('/connections', [ManufacturerController::class, 'connections'])->name('connections.index');
    Route::get('/reports', [ManufacturerController::class, 'reports'])->name('reports.index');
});