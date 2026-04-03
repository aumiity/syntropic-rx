<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->foreignId('dosage_id')->nullable()->constrained('label_dosages')->nullOnDelete();
            $table->unsignedBigInteger('meal_relation_id')->nullable();
            $table->foreign('meal_relation_id')->references('id')->on('label_meal_relations')->nullOnDelete();
            $table->foreignId('label_time_id')->nullable()->constrained('label_times')->nullOnDelete();
            $table->foreignId('advice_id')->nullable()->constrained('label_advices')->nullOnDelete();
            $table->boolean('show_barcode')->default(false);
            $table->boolean('is_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_labels', function (Blueprint $table) {
            $table->dropForeign(['dosage_id']);
            $table->dropForeign(['meal_relation_id']);
            $table->dropForeign(['label_time_id']);
            $table->dropForeign(['advice_id']);

            $table->dropColumn([
                'dosage_id',
                'meal_relation_id',
                'label_time_id',
                'advice_id',
                'show_barcode',
                'is_default',
            ]);
        });
    }
};
