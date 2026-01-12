<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderController::class, 'showForm'])->name('user.form');
Route::post('/submit-identity', [OrderController::class, 'submitIdentity'])->name('user.submitIdentity');
Route::post('/customer/logout', [OrderController::class, 'logout'])
    ->name('customer.logout');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::post('/add-to-cart', [OrderController::class, 'addToCart'])->name('cart.add');
Route::post('/update-quantity', [OrderController::class, 'updateQuantity'])->name('cart.update');
Route::post('/clear-cart', [OrderController::class, 'clearCart'])->name('cart.clear');
Route::get('/cart', [OrderController::class, 'cart'])->name('cart.index');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/order/status/{order_id}', [OrderController::class, 'status'])->name('order.status');
Route::get('/order/{order_id}/receipt', [OrderController::class, 'showReceipt'])->name('order.receipt');
Route::post('/order/{order_id}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');
Route::post('/order/{order_id}/update-payment', [OrderController::class, 'updatePaymentStatus'])->name('order.updatePayment');
Route::post('/midtrans/notification', [OrderController::class, 'notificationHandler'])->name('midtrans.notification');
Route::get('/orders', [OrderController::class, 'orders'])->name('orders.index');

// Admin Routes - Protected dengan middleware auth + admin check
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/order/{id}/assign', [AdminController::class, 'assignToSelf'])->name('assign');
    Route::post('/order/{id}/complete', [AdminController::class, 'completeOrder'])->name('complete');
    Route::get('/order/{id}/struk', [AdminController::class, 'generateStruk'])->name('struk');
    
    // Menu CRUD
    Route::get('/menu', [AdminController::class, 'menuIndex'])->name('menu.index');
    Route::get('/menu/create', [AdminController::class, 'menuCreate'])->name('menu.create');
    Route::post('/menu', [AdminController::class, 'menuStore'])->name('menu.store');
    Route::get('/menu/{id}/edit', [AdminController::class, 'menuEdit'])->name('menu.edit');
    Route::put('/menu/{id}', [AdminController::class, 'menuUpdate'])->name('menu.update');
    Route::delete('/menu/{id}', [AdminController::class, 'menuDestroy'])->name('menu.destroy');
    
    // Financial Report
    Route::get('/financial', [AdminController::class, 'financialReport'])->name('financial');

    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
});

// Auth Routes (jangan lupa include)
require __DIR__.'/auth.php';