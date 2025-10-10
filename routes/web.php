<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderController::class, 'showForm'])->name('user.form');
Route::post('/submit-identity', [OrderController::class, 'submitIdentity'])->name('user.submitIdentity');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::post('/add-to-cart', [OrderController::class, 'addToCart'])->name('cart.add');
Route::post('/update-quantity', [OrderController::class, 'updateQuantity'])->name('cart.update');
Route::get('/cart', [OrderController::class, 'cart'])->name('cart.index');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/order/status/{order_id}', [OrderController::class, 'status'])->name('order.status');
Route::post('/midtrans/notification', [OrderController::class, 'notificationHandler'])->name('midtrans.notification');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/order/{id}/assign', [AdminController::class, 'assignToSelf'])->name('admin.assign');
    Route::post('/admin/order/{id}/complete', [AdminController::class, 'completeOrder'])->name('admin.complete');
    Route::get('/admin/order/{id}/struk', [AdminController::class, 'generateStruk'])->name('admin.struk');
});

