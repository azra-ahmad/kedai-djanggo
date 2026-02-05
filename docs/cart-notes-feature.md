# Cart Notes Feature - Complete Walkthrough

## Overview

Implementasi lengkap fitur **Cart Notes** dari perbaikan bug hingga enhancement UI.

---

## 🐛 Bug Fix: Notes Tidak Masuk ke Database

### Problem
Notes yang diinput customer di modal tidak tersimpan ke database (`order_items.note` selalu `null`).

### Root Cause Analysis
1. **CheckoutController::process()** - Tidak menyertakan `note` saat create `OrderItem`
2. **OrderItem Model** - Field `note` tidak ada di `$fillable` array

### Solution

#### [CheckoutController.php](file:///C:/laragon/www/kedai-djanggo/app/Http/Controllers/Customer/CheckoutController.php)

```diff
 OrderItem::create([
     'order_id' => $order->id,
     'menu_id' => $menu_id,
     'jumlah' => $item['quantity'],
     'subtotal' => $item['price'] * $item['quantity'],
+    'note' => $item['note'] ?? null,
 ]);
```

#### [OrderItem.php](file:///C:/laragon/www/kedai-djanggo/app/Models/OrderItem.php)

```diff
 protected $fillable = [
     'order_id',
     'menu_id',
     'jumlah',
     'subtotal',
+    'note',
 ];
```

---

## 🔄 Cart Logic: Separate Items by Note

### Problem
Menambahkan menu yang sama dengan note berbeda akan merge jadi 1 item (note ter-overwrite).

### Solution
Ubah grouping dari `menu_id` → `menu_id + note`

#### [CartController.php](file:///C:/laragon/www/kedai-djanggo/app/Http/Controllers/Customer/CartController.php)

```diff
 $existingItem = DB::table('carts')
     ->where('session_id', $sessionId)
     ->where('menu_id', $menuId)
+    ->where('note', $note)  // Note is part of unique key
     ->first();
```

**Behavior:**
- "Kopi Hitam" + "tanpa gula" = 1 item
- "Kopi Hitam" + "less sugar" = **separate item**

---

## 📝 Checkout: Inline Note Editing

### New Endpoint
`POST /update-note` → `cart.updateNote`

#### [CartController.php](file:///C:/laragon/www/kedai-djanggo/app/Http/Controllers/Customer/CartController.php)

```php
public function updateNote(Request $request)
{
    $updated = DB::table('carts')
        ->where('id', $request->cart_id)
        ->where('session_id', $this->getSessionId())
        ->update(['note' => $request->note ?? '']);
    
    return response()->json(['message' => 'Note updated']);
}
```

#### [checkout.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/user/checkout.blade.php)

- Full-width button dengan placeholder "+ Tambah catatan (tanpa es, pedas, dll)"
- Amber background saat note sudah diisi
- Inline text input dengan tombol ✓ dan ✕

---

## 👨‍🍳 Admin: Notes Display

### 1. Orders Table (Desktop)

#### [orders.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/admin/orders.blade.php)

Items column menampilkan note dengan format:
```
2x Kopi Hitam 📝
↳ tanpa gula
```

### 2. Dashboard Kitchen Queue

#### [dashboard.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/admin/dashboard.blade.php)

Setiap item di Kitchen Queue menampilkan note dalam amber badge.

### 3. Admin Receipt (Struk)

#### [admin/struk.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/admin/struk.blade.php)

```
Kopi Hitam                  1      15.000
→ tanpa gula
```

---

## 📱 Customer: UI Consistency

### User Receipt (Struk)

#### [user/struk.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/user/struk.blade.php)

- **Redesigned** to match admin thermal receipt style
- Notes displayed per item
- Indonesian status labels

### Order Status Page

#### [user/status.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/user/status.blade.php)

Added note display: `📝 tanpa gula` in amber background

### Pesanan Saya Pages

#### [user/orders.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/user/orders.blade.php)
#### [components/customer/orders-list.blade.php](file:///C:/laragon/www/kedai-djanggo/resources/views/components/customer/orders-list.blade.php)

**Indonesian Status Labels:**
| Status | Label | Color |
|--------|-------|-------|
| `pending` | Menunggu | Kuning |
| `paid` | Dibayar | Hijau |
| `done` | Selesai | Biru |
| `failed` | Gagal | Merah |

---

## 📁 Files Modified

| File | Changes |
|------|---------|
| `CartController.php` | Group by menu_id+note, updateNote(), update() uses cart_id |
| `CheckoutController.php` | Include cart_id in cart array, pass note to OrderItem |
| `OrderItem.php` | Added `note` to fillable |
| `routes/web.php` | Added `cart.updateNote` route |
| `checkout.blade.php` | Inline note editing UI with saveNote() |
| `admin/orders.blade.php` | Notes in Items column (desktop) |
| `admin/dashboard.blade.php` | Notes in Kitchen Queue |
| `admin/struk.blade.php` | Notes per item |
| `user/struk.blade.php` | Redesigned to match admin style |
| `user/status.blade.php` | Notes per item |
| `user/orders.blade.php` | Indonesian status labels |
| `orders-list.blade.php` | Indonesian status labels |

---

## ✅ Testing Checklist

- [x] Add item with note via modal → saved to cart
- [x] Same menu + different notes → separate cart items
- [x] Edit note on checkout page → updates in database
- [x] Complete order → notes appear in order_items table
- [x] Admin orders (desktop) → shows notes
- [x] Admin dashboard Kitchen Queue → shows notes
- [x] Admin struk → prints notes
- [x] Customer struk → shows notes
- [x] Customer order status → shows notes
- [x] All pages use Indonesian status labels
