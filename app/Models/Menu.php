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
        'is_available',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_available' => 'boolean',
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

    // Accessor untuk URL gambar otomatis (Handle External URL, Local Storage, & Default)
    public function getImageUrlAttribute()
    {
        if ($this->gambar && (str_starts_with($this->gambar, 'http') || str_starts_with($this->gambar, 'https'))) {
            return $this->gambar;
        }

        if ($this->gambar) {
            return asset('storage/menu-images/' . $this->gambar);
        }

        return asset('images/default-menu.jpg');
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