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
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shop_name', 200)->nullable();
            $table->text('shop_address')->nullable();
            $table->string('shop_phone', 50)->nullable();
            $table->string('shop_license_no', 100)->nullable()->comment('เลขใบอนุญาต');
            $table->string('shop_line_id', 100)->nullable();
            $table->string('shop_tax_id', 20)->nullable()->comment('เลขประจำตัวผู้เสียภาษี');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
