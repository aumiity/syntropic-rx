<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drug_types', function (Blueprint $table) {
            $table->tinyInteger('is_fda9')->default(0);
            $table->tinyInteger('is_fda10')->default(0);
            $table->tinyInteger('is_fda11')->default(0);
            $table->tinyInteger('is_fda13')->default(0);
        });

        // Update rows with specified values
        $updates = [
            'GENERAL' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 0],
            'OTC' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 0],
            'DANGEROUS' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 1, 'is_fda13' => 0],
            'SPCL_CTRL' => ['is_fda9' => 1, 'is_fda10' => 1, 'is_fda11' => 0, 'is_fda13' => 0],
            'PSYCHO_3' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 1],
            'PSYCHO_4' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 1],
            'NARCOTIC_3' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 1],
            'TRADITIONAL' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 0],
            'EXTERNAL' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 1, 'is_fda13' => 0],
            'HERB' => ['is_fda9' => 1, 'is_fda10' => 0, 'is_fda11' => 0, 'is_fda13' => 0],
        ];

        foreach ($updates as $code => $values) {
            DB::table('drug_types')
                ->where('code', $code)
                ->update($values);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_types', function (Blueprint $table) {
            $table->dropColumn(['is_fda9', 'is_fda10', 'is_fda11', 'is_fda13']);
        });
    }
};
