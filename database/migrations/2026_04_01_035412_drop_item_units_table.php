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
        Schema::table('products', function (Blueprint $table) {
            foreach (['unit_id', 'unit_small_id', 'unit_large_id'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Throwable $e) {
                        // Ignore when FK does not exist.
                    }
                }
            }
        });

        Schema::dropIfExists('item_units');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['unit_id', 'unit_small_id', 'unit_large_id'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    try {
                        $table->foreign($column)->references('id')->on('item_units');
                    } catch (\Throwable $e) {
                        // Ignore when FK already exists.
                    }
                }
            }
        });
    }
};
