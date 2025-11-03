<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_menu',
        'kategori_menu',
        'harga',
        'gambar',
        'description',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // Konstanta untuk kategori yang valid
    public const KATEGORI_VALID = [
        'makanan',
        'minuman',
        'dessert',
        'kopi',
        'cemilan'
    ];

    // Accessor untuk format kategori yang lebih rapi
    public function getFormattedKategoriAttribute()
    {
        return ucfirst($this->kategori_menu);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope untuk filter by kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_menu', $kategori);
    }
}