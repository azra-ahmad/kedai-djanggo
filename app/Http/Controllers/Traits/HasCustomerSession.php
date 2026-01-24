<?php

namespace App\Http\Controllers\Traits;

use App\Models\Customer;

trait HasCustomerSession
{
    /**
     * Get customer from session token
     * Returns null if token invalid or customer not found
     */
    protected function getCustomer()
    {
        $token = session('customer_token');
        if (!$token) {
            return null;
        }
        return Customer::where('customer_token', $token)->first();
    }
}
