<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelAdviceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_advices')->insertOrIgnore([
            ['name_th' => 'ทานให้ครบตามที่แพทย์สั่ง', 'sort_order' => 0],
            ['name_th' => 'ทานติดต่อกันจนหมด', 'sort_order' => 1],
            ['name_th' => 'ห้ามหยุดยาเอง', 'sort_order' => 2],
            ['name_th' => 'ห้ามทานกับนมหรือยาลดกรด', 'sort_order' => 3],
            ['name_th' => 'ควรดื่มน้ำมากๆ ขณะทานยา', 'sort_order' => 4],
            ['name_th' => 'อาจทำให้ง่วงซึม ระวังการขับขี่', 'sort_order' => 5],
            ['name_th' => 'เก็บในที่เย็น ไม่โดนแสงแดด', 'sort_order' => 6],
            ['name_th' => 'ระวัง อาจทำให้คลื่นไส้', 'sort_order' => 7],
        ]);
    }
}
