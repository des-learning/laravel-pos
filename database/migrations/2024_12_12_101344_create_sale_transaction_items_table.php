<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_transaction_id')->references('id')->on('sale_transactions');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->string('product_sku');
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->integer('quantity_sold');
            $table->decimal('total_amount', 10, 2)
                ->storedAs('product_price * quantity_sold');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_transaction_items');
    }
};
