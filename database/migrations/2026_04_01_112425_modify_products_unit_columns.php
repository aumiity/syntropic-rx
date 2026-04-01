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
            // ADD unit_name column after category_id
            $table->string('unit_name')->nullable()->after('category_id');

            // DROP foreign key constraints (check if exist before dropping)
            try {
                // Get table columns to check constraint names
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->listTableDetails('products');
                $foreignKeys = $doctrineTable->getForeignKeys();
                
                // Drop foreign keys if they exist
                foreach ($foreignKeys as $fk) {
                    if (in_array($fk->getLocalColumns(), [['unit_id'], ['unit_large_id'], ['unit_small_id']])) {
                        $table->dropForeign($fk->getName());
                    }
                }
            } catch (\Exception $e) {
                // Silently continue if FK doesn't exist
            }

            // DROP columns: unit_id, unit_large_id, unit_small_id, conversion
            if (Schema::hasColumn('products', 'unit_id')) {
                $table->dropColumn('unit_id');
            }

            if (Schema::hasColumn('products', 'unit_large_id')) {
                $table->dropColumn('unit_large_id');
            }

            if (Schema::hasColumn('products', 'unit_small_id')) {
                $table->dropColumn('unit_small_id');
            }

            if (Schema::hasColumn('products', 'conversion')) {
                $table->dropColumn('conversion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Re-add columns in reverse order
            $table->integer('conversion')->default(1)->after('category_id');
            $table->unsignedBigInteger('unit_small_id')->nullable()->after('conversion');
            $table->unsignedBigInteger('unit_large_id')->nullable()->after('unit_small_id');
            $table->unsignedBigInteger('unit_id')->nullable()->after('unit_large_id');
            
            // Re-add unit_name (will already exist from up, but ensure it's dropped)
            // Actually, we need to remove it first
            if (Schema::hasColumn('products', 'unit_name')) {
                $table->dropColumn('unit_name');
            }

            // Re-add foreign key constraints
            $table->foreign('unit_id')->references('id')->on('item_units')->onDelete('set null');
            $table->foreign('unit_large_id')->references('id')->on('item_units')->onDelete('set null');
            $table->foreign('unit_small_id')->references('id')->on('item_units')->onDelete('set null');
        });
    }
};
