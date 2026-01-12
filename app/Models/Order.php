<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_token',
        'admin_id',
        'total_harga',
        'metode_pembayaran',
        'status',
        'snap_token',
        'midtrans_order_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}