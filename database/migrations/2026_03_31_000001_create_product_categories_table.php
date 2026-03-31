<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();
        });

        DB::table('product_categories')->insert([
            ['code' => 'DRUG',       'name' => 'ยา',           'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SUPPLY',     'name' => 'เวชภัณฑ์',      'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SUPPLEMENT', 'name' => 'อาหารเสริม',    'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'HERB',       'name' => 'สมุนไพร',       'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CONTRACEPT', 'name' => 'ยาคุมกำเนิด',   'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'OTHER',      'name' => 'อื่นๆ',          'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};