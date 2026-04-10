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
        Schema::table('sale_item_lots', function (Blueprint $table) {
            $table->dropForeign('sale_item_lots_lot_id_foreign');
            $table->unsignedBigInteger('lot_id')->nullable()->change();
            $table->foreign('lot_id')->references('id')->on('product_lots')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_item_lots', function (Blueprint $table) {
            $table->dropForeign('sale_item_lots_lot_id_foreign');
            $table->unsignedBigInteger('lot_id')->nullable(false)->change();
            $table->foreign('lot_id')->references('id')->on('product_lots');
        });
    }
};