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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_lot_id_foreign');
            $table->unsignedBigInteger('lot_id')->nullable()->change();
            $table->foreign('lot_id')->references('id')->on('product_lots')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_lot_id_foreign');
            $table->unsignedBigInteger('lot_id')->nullable(false)->change();
            $table->foreign('lot_id')->references('id')->on('product_lots');
        });
    }
};