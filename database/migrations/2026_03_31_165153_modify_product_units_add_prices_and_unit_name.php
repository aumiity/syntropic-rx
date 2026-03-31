<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            // unit_id, unit_name, price_retail, price_wholesale1 already migrated
            // Only add missing price_wholesale2
            $table->decimal('price_wholesale2', 10, 2)->default(0)->after('price_wholesale1');
        });
    }

    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn('price_wholesale2');
        });
    }
};
