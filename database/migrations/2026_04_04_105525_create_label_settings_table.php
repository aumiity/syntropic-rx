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
        Schema::create('label_settings', function (Blueprint $table) {
            $table->id();
            // หน้ากระดาษ
            $table->integer('paper_width')->default(100);       // mm
            $table->integer('paper_height')->default(75);       // mm
            $table->integer('padding_top')->default(3);         // mm
            $table->integer('padding_right')->default(3);       // mm
            $table->integer('padding_bottom')->default(3);      // mm
            $table->integer('padding_left')->default(3);        // mm
            // Font
            $table->string('font_family')->default('Tahoma');
            $table->integer('font_size_shop')->default(13);     // px ชื่อร้าน
            $table->integer('font_size_product')->default(14);  // px ชื่อยา
            $table->integer('font_size_dosage')->default(16);   // px วิธีใช้
            $table->integer('font_size_small')->default(10);    // px indication/advice
            // ตัวหนา
            $table->boolean('bold_shop')->default(true);
            $table->boolean('bold_product')->default(true);
            $table->boolean('bold_dosage')->default(true);
            // ระยะห่าง
            $table->float('line_spacing')->default(1.4);
            $table->integer('section_gap')->default(4);         // px ระยะห่างระหว่าง section
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_settings');
    }
};
