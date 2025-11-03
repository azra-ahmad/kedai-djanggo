<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM kategori_menu untuk include kopi dan cemilan
        DB::statement("ALTER TABLE menus MODIFY kategori_menu ENUM('makanan', 'minuman', 'dessert', 'kopi', 'cemilan') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum lama (tapi hati-hati kalau ada data kopi/cemilan!)
        DB::statement("ALTER TABLE menus MODIFY kategori_menu ENUM('makanan', 'minuman', 'dessert') NOT NULL");
    }
};