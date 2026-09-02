<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopOwnerController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route::get('test-direct-payment', function () {

//     $safepay = new \Safepay\SafepayClient([
//         'api_key' => 'sec_ed9ccaca-5ff2-413a-892c-fb6a33ef1c74',
//         'api_base' => 'https://sandbox.api.getsafepay.com' // for live payments use https://api.getsafepay.com
// ]);

// try {
//   $session = $safepay->order->setup([
//     "merchant_api_key" => "sec_ed9ccaca-5ff2-413a-892c-fb6a33ef1c74",
//     "intent" => "CYBERSOURCE",
//     "mode" => "payment",
//     "entry_mode" => "raw",
//     "currency" => "USD",
//     "amount" => 10000,
//     "metadata" => [
//       "order_id" => "1234567890"
//     ],
//     "include_fees" => false
//   ]);

//   dd($session);

// } catch(\UnexpectedValueException $e) {
//     // Invalid payload
//     http_response_code(400);
//     die('Invalid payload');
//     exit();
// }



// });

Route::middleware(['auth'])->group(function () {
    Route::get('/connections/search', [ConnectionController::class, 'search'])->name('connections.search');
    Route::post('/connections/request', [ConnectionController::class, 'store'])->name('connections.store');
    Route::post('/connections/{id}/accept', [ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connections/{id}/reject', [ConnectionController::class, 'reject'])->name('connections.reject');
    Route::delete('/connections/{id}', [ConnectionController::class, 'destroy'])->name('connections.destroy');
    Route::get('/user/{id}', [ConnectionController::class, 'showProfile'])->name('user.show');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'readAndRedirect'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // Chat routes (accessible to both shop_owner and manufacturer)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{connection}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{connection}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{connection}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/{connection}/read', [ChatController::class, 'markRead'])->name('chat.read');
});

// Basic Shop Routes (Protected)
Route::middleware(['auth', 'role:shop_owner'])->group(function () {
    Route::get('/shop/dashboard', [ShopOwnerController::class, 'dashboard'])->name('shop.dashboard');
    Route::get('/shop/profile', [ShopOwnerController::class, 'profile'])->name('shop.profile');
    Route::put('/shop/profile', [ShopOwnerController::class, 'updateProfile'])->name('shop.profile.update');
    Route::post('/shop/profile/image', [ShopOwnerController::class, 'uploadProfileImage'])->name('shop.profile.image.upload');
    Route::delete('/shop/profile/image', [ShopOwnerController::class, 'deleteProfileImage'])->name('shop.profile.image.delete');
    
    // Shop Order Routes
    Route::get('/shop/orders', [ShopOwnerController::class, 'orders'])->name('shop.orders.index');
    Route::get('/shop/orders/create', [ShopOwnerController::class, 'createOrder'])->name('shop.orders.create');
    Route::post('/shop/orders', [ShopOwnerController::class, 'storeOrder'])->name('shop.orders.store');
    Route::get('/shop/api/manufacturers/{id}/products', [ShopOwnerController::class, 'getProducts'])->name('shop.api.manufacturers.products');
    Route::get('/shop/api/products/{id}/variants', [ShopOwnerController::class, 'getVariants'])->name('shop.api.products.variants');
    Route::get('/shop/orders/{id}', [ShopOwnerController::class, 'showOrder'])->name('shop.orders.show');
    Route::post('/shop/orders/{id}/cancel', [ShopOwnerController::class, 'cancelOrder'])->name('shop.orders.cancel');
    Route::post('/shop/orders/{id}/confirm-delivery', [ShopOwnerController::class, 'confirmDelivery'])->name('shop.orders.confirm-delivery');
    
    // Other Shop Pages
    Route::get('/shop/connections', [ShopOwnerController::class, 'connections'])->name('shop.connections');
    Route::get('/shop/payments', [ShopOwnerController::class, 'payments'])->name('shop.payments');
    Route::get('/shop/reports', [ShopOwnerController::class, 'reports'])->name('shop.reports');

    // Payment Checkout Routes
    Route::get('/shop-owner/orders/{order}/pay', [PaymentController::class, 'showPaymentForm'])->name('shop.orders.pay');
    Route::post('/shop-owner/orders/{order}/pay/stripe/initiate', [PaymentController::class, 'initiateStripePayment'])->name('shop.orders.pay.stripe.initiate');
    Route::post('/shop-owner/orders/{order}/pay/stripe/confirm', [PaymentController::class, 'confirmStripePayment'])->name('shop.orders.pay.stripe.confirm');
    Route::post('/shop-owner/orders/{order}/pay/safepay/initiate', [PaymentController::class, 'initiateSafepayPayment'])->name('shop.orders.pay.safepay.initiate');
    Route::get('/shop/orders/{order}/payment-status', [PaymentController::class, 'getPaymentStatus'])->name('shop.orders.payment-status');
});

// Safepay Webhook (Public, no auth)
Route::post('/safepay/webhook', [PaymentController::class, 'safepayWebhook'])->name('safepay.webhook');

// Stripe Webhook (Public, no auth)
Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');

// Manufacturer Routes (Protected)
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [ManufacturerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ManufacturerController::class, 'profile'])->name('profile');
    Route::put('/profile', [ManufacturerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/image', [ManufacturerController::class, 'uploadProfileImage'])->name('profile.image.upload');
    Route::delete('/profile/image', [ManufacturerController::class, 'deleteProfileImage'])->name('profile.image.delete');
    
    // Stripe onboarding routes
    Route::get('/stripe/connect', [StripeConnectController::class, 'connect'])->name('stripe.connect');
    Route::get('/stripe/callback', [StripeConnectController::class, 'callback'])->name('stripe.callback');
    Route::post('/stripe/disconnect', [StripeConnectController::class, 'disconnect'])->name('stripe.disconnect');

    Route::get('/payment-methods', fn () => redirect()->route('manufacturer.payments.index', ['tab' => 'methods']))->name('payment-methods');
    Route::post('/payment-methods', [ManufacturerController::class, 'updatePaymentMethods'])->name('payment-methods.update');
    Route::get('/orders', [ManufacturerController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{id}', [ManufacturerController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/accept', [ManufacturerController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{id}/reject', [ManufacturerController::class, 'rejectOrder'])->name('orders.reject');
    Route::post('/orders/{id}/cancel', [ManufacturerController::class, 'cancelAcceptedOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/stages/{stageId}/toggle', [ManufacturerController::class, 'toggleStage'])->name('orders.stages.toggle');
    Route::get('/catalog', [ManufacturerController::class, 'catalog'])->name('catalog.index');
    Route::get('/catalog/create', [ManufacturerController::class, 'createProduct'])->name('catalog.create');
    Route::post('/catalog', [ManufacturerController::class, 'storeProduct'])->name('catalog.store');
    Route::get('/catalog/{id}/edit', [ManufacturerController::class, 'editProduct'])->name('catalog.edit');
    Route::put('/catalog/{id}', [ManufacturerController::class, 'updateProduct'])->name('catalog.update');
    Route::get('/catalog/{id}', [ManufacturerController::class, 'showProduct'])->name('catalog.show');
    Route::delete('/catalog/{id}', [ManufacturerController::class, 'destroyProduct'])->name('catalog.destroy');

    Route::get('/production', [ManufacturerController::class, 'production'])->name('production.index');
    Route::get('/payments', [ManufacturerController::class, 'payments'])->name('payments.index');
    Route::get('/connections', [ManufacturerController::class, 'connections'])->name('connections.index');
    Route::get('/reports', [ManufacturerController::class, 'reports'])->name('reports.index');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/toggle-active', [AdminController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/toggle-verified', [AdminController::class, 'toggleVerified'])->name('users.toggle-verified');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/image', [AdminController::class, 'uploadProfileImage'])->name('profile.image.upload');
    Route::delete('/profile/image', [AdminController::class, 'deleteProfileImage'])->name('profile.image.delete');
});