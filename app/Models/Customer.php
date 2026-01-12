<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'customer_token',
    ];

    /**
     * Boot the model and generate UUID for customer_token on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_token)) {
                $customer->customer_token = Str::uuid()->toString();
            }
        });
    }

    /**
     * Find customer by token
     */
    public static function findByToken(?string $token)
    {
        if (!$token) {
            return null;
        }

        return static::where('customer_token', $token)->first();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}