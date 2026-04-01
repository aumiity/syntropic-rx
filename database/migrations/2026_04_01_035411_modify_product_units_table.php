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
        if (Schema::hasColumn('product_units', 'unit_id')) {
            Schema::table('product_units', function (Blueprint $table) {
                try {
                    $table->dropForeign(['unit_id']);
                } catch (\Throwable $e) {
                    // Ignore when foreign key does not exist in some environments.
                }

                $table->dropColumn('unit_id');
            });
        }

        Schema::table('product_units', function (Blueprint $table) {
            if (! Schema::hasColumn('product_units', 'unit_name')) {
                $table->string('unit_name')->after('product_id');
            }

            if (! Schema::hasColumn('product_units', 'is_base_unit')) {
                $table->boolean('is_base_unit')->default(false)->after('qty_per_base');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (Schema::hasColumn('product_units', 'is_base_unit')) {
                $table->dropColumn('is_base_unit');
            }

            if (Schema::hasColumn('product_units', 'unit_name')) {
                $table->dropColumn('unit_name');
            }
        });

        if (! Schema::hasColumn('product_units', 'unit_id')) {
            Schema::table('product_units', function (Blueprint $table) {
                $table->foreignId('unit_id')->nullable()->after('product_id')->constrained('item_units');
            });
        }
    }
};
