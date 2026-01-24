<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;

class MidtransController extends Controller
{
    /**
     * Handle Midtrans payment notification webhook
     */
    public function handle(Request $request)
    {
        \Log::info('MIDTRANS WEBHOOK HIT', $request->all());
        try {
            \Log::info('Midtrans notification received:', $request->all());
            
            // Check if this is from frontend callback or Midtrans webhook
            $isFrontendCallback = $request->has('order_id') && !$request->has('transaction_id');
            
            if ($isFrontendCallback) {
                // Handle frontend callback
                $orderId = $request->order_id;
                $transactionStatus = $request->transaction_status;
                
                \Log::info('Frontend callback:', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus
                ]);
                
                $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
                
                if ($transactionStatus == 'settlement') {
                    // UX ONLY: Redirect or show success, but DO NOT update DB here
                    // DB update must happen via webhook for security & reliability
                    \Log::info('Frontend callback received for order: ' . $orderId);
                }
                
                return response()->json(['status' => 'success', 'source' => 'frontend']);
            } else {
                // Handle Midtrans webhook with proper notification class
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production');
                
                $notification = new \Midtrans\Notification();
                
                $transactionStatus = $notification->transaction_status;
                $orderId = $notification->order_id;
                $fraudStatus = isset($notification->fraud_status) ? $notification->fraud_status : 'accept';
                
                \Log::info('Midtrans webhook:', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus
                ]);
                
                $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
                
                \Log::info('Order found for webhook:', ['db_id' => $order->id, 'midtrans_id' => $order->midtrans_order_id]);

                // Update status based on transaction status
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $order->update(['status' => 'paid']);
                        \Log::info('Order status updated to paid (capture)');
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $order->update(['status' => 'paid']);
                    \Log::info('Order status updated to paid (settlement)');
                } elseif ($transactionStatus == 'pending') {
                    // Do not revert to pending if already paid (idempotency)
                    if ($order->status !== 'paid') {
                        $order->update(['status' => 'pending']);
                    }
                    \Log::info('Order status kept as pending');
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $order->update(['status' => 'failed']);
                    \Log::info('Order status updated to failed');
                }
                
                return response()->json(['status' => 'success', 'source' => 'webhook']);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans notification error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
