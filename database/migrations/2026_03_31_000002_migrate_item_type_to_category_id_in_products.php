<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: เพิ่ม category_id (nullable ก่อน)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('item_type')
                  ->constrained('product_categories')
                  ->nullOnDelete();
        });

        // Step 2: copy ค่าเดิมจาก item_type → category_id
        DB::table('products')->where('item_type', 'drug')     ->update(['category_id' => 1]);
        DB::table('products')->where('item_type', 'supply')   ->update(['category_id' => 2]);
        DB::table('products')->where('item_type', 'equipment')->update(['category_id' => 6]); // → อื่นๆ
        DB::table('products')->where('item_type', 'service')  ->update(['category_id' => 6]); // → อื่นๆ

        // Step 3: ลบ item_type ออก
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('item_type', ['drug', 'supply', 'equipment', 'service'])
                  ->default('drug')
                  ->nullable()
                  ->after('category_id');
        });

        DB::table('products')->where('category_id', 1)->update(['item_type' => 'drug']);
        DB::table('products')->where('category_id', 2)->update(['item_type' => 'supply']);

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};