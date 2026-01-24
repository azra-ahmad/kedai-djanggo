<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasCustomerSession;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class IdentityController extends Controller
{
    use HasCustomerSession;

    /**
     * Show customer identity form
     */
    public function showForm()
    {
        // Clear customer session if requested (preserve admin auth)
        if (request()->has('new_session')) {
            Session::forget('customer_token');
            Session::forget('cart');
        }
        
        // Check if customer token exists and is valid
        if ($this->getCustomer()) {
            return redirect()->route('menu.index');
        }
        
        return view('user.form');
    }

    /**
     * Submit customer identity
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|numeric|digits_between:10,15',
        ], [
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.digits_between' => 'Nomor telepon minimal 10 digit dan maksimal 15 digit'
        ]);

        // ALWAYS create a new customer record for every session
        // This ensures strict separation of identities even if phone number is reused
        $customer = Customer::create([
            'phone' => $validated['phone'],
            'name' => $validated['name'],
            'customer_token' => Str::uuid()->toString(),
        ]);

        // Clear only customer-related session data (preserve admin auth)
        Session::forget('customer_token');
        Session::forget('cart');
        
        // Set customer token in session
        session(['customer_token' => $customer->customer_token]);
        
        return redirect()->route('menu.index');
    }

    /**
     * Logout customer (clear session)
     */
    public function logout()
    {
        // Explicit customer logout
        Session::forget('customer_token');
        Session::forget('cart');
        
        // Do NOT use flush() or Auth::logout() to protect admin session
        
        return redirect()->route('user.form');
    }
}
