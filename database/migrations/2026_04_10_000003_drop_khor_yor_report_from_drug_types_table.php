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
        Schema::table('drug_types', function (Blueprint $table) {
            $table->dropColumn('khor_yor_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_types', function (Blueprint $table) {
            $table->string('khor_yor_report')->nullable();
        });
    }
};
