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
        Schema::create('product_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('label_name', 200)->nullable();
            $table->decimal('dose_qty', 5, 2)->nullable();
            $table->foreignId('frequency_id')->nullable()->constrained('label_frequencies')->nullOnDelete();
            $table->foreignId('timing_id')->nullable()->constrained('label_timings')->nullOnDelete();
            $table->text('indication_th')->nullable();
            $table->text('indication_mm')->nullable();
            $table->text('indication_zh')->nullable();
            $table->text('note_th')->nullable();
            $table->text('note_mm')->nullable();
            $table->text('note_zh')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('product_id');
            $table->index('frequency_id');
            $table->index('timing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_labels');
    }
};
