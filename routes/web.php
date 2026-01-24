<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Webhook;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// CUSTOMER ROUTES
// ============================================================================

// Customer Identity
Route::get('/', [Customer\IdentityController::class, 'showForm'])->name('user.form');
Route::post('/submit-identity', [Customer\IdentityController::class, 'submit'])->name('user.submitIdentity');
Route::post('/customer/logout', [Customer\IdentityController::class, 'logout'])->name('customer.logout');

// Menu (public)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Cart
Route::get('/cart', [Customer\CartController::class, 'index'])->name('cart.index');
Route::post('/add-to-cart', [Customer\CartController::class, 'add'])->name('cart.add');
Route::post('/update-quantity', [Customer\CartController::class, 'update'])->name('cart.update');
Route::post('/clear-cart', [Customer\CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [Customer\CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout/process', [Customer\CheckoutController::class, 'process'])->name('checkout.process');

// Customer Orders
Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders.index');
Route::get('/order/status/{order_id}', [Customer\OrderController::class, 'status'])->name('order.status');
Route::get('/order/{order_id}/receipt', [Customer\OrderController::class, 'receipt'])->name('order.receipt');
Route::post('/order/{order_id}/cancel', [Customer\OrderController::class, 'cancel'])->name('order.cancel');
// ============================================================================
// WEBHOOK ROUTES (CSRF exempt - configured in VerifyCsrfToken middleware)
// ============================================================================

Route::post('/midtrans/notification', [Webhook\MidtransController::class, 'handle'])->name('midtrans.notification');

// ============================================================================
// DEVELOPMENT BACKDOOR: Force Pay (Only works in Local env)
// ============================================================================
if (app()->environment('local')) {
    Route::get('/debug/force-pay/{id}', function ($id) {
        $order = \App\Models\Order::findOrFail($id);
        // Only update if not already paid to simulate webhook behavior
        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);
        }
        return redirect()->route('order.status', $id)->with('success', '[DEV] Order FORCE PAID');
    });
}

// ============================================================================
// ADMIN ROUTES - Protected with auth middleware
// ============================================================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/check-new-orders', [Admin\DashboardController::class, 'checkNewOrders'])->name('check.orders');
    
    // Orders Management
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders');
    Route::post('/order/{id}/assign', [Admin\OrderController::class, 'assign'])->name('assign');
    Route::post('/order/{id}/complete', [Admin\OrderController::class, 'complete'])->name('complete');
    Route::post('/order/{id}/fail', [Admin\OrderController::class, 'fail'])->name('fail');
    Route::get('/order/{id}/struk', [Admin\OrderController::class, 'receipt'])->name('struk');
    
    // Menu CRUD
    Route::get('/menu', [Admin\MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [Admin\MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [Admin\MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{id}/edit', [Admin\MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{id}', [Admin\MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}', [Admin\MenuController::class, 'destroy'])->name('menu.destroy');
    
    // Financial Report
    Route::get('/financial', [Admin\ReportController::class, 'financial'])->name('financial');

    // Profile
    Route::get('/profile', [Admin\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
});

// ============================================================================
// AUTH ROUTES
// ============================================================================

require __DIR__.'/auth.php';