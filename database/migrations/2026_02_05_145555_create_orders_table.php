<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('orders_customer_id_foreign');
            $table->char('customer_token', 36)->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index('orders_admin_id_foreign');
            $table->decimal('total_harga', 10);
            $table->string('metode_pembayaran', 20)->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'done'])->default('pending')->index();
            $table->string('snap_token')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
