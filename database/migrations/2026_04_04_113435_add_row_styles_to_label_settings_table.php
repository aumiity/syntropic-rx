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
        Schema::table('label_settings', function (Blueprint $table) {
            $table->json('row_styles')->nullable()->after('section_gap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('label_settings', function (Blueprint $table) {
            $table->dropColumn('row_styles');
        });
    }
};
