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
        Schema::create('label_advices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_th', 200);
            $table->string('name_en', 200)->nullable();
            $table->string('name_mm', 200)->nullable();
            $table->string('name_zh', 200)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_advices');
    }
};
