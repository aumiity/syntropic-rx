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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('unit_id')->constrained('item_units');
            $table->string('barcode')->unique()->nullable();
            $table->decimal('qty_per_base', 10, 4);
            $table->decimal('price_retail', 10, 2)->nullable();
            $table->decimal('price_wholesale1', 10, 2)->nullable();
            $table->boolean('is_for_sale')->default(false);
            $table->boolean('is_for_purchase')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
