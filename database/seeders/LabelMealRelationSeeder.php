<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelMealRelationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_meal_relations')->insertOrIgnore([
            ['code' => '01', 'name_th' => 'ก่อนอาหาร', 'name_en' => 'before meal', 'sort_order' => 0],
            ['code' => '02', 'name_th' => 'หลังอาหาร', 'name_en' => 'after meal', 'sort_order' => 1],
            ['code' => '03', 'name_th' => 'พร้อมอาหาร', 'name_en' => 'with meal', 'sort_order' => 2],
            ['code' => '04', 'name_th' => 'หลังอาหารทันที', 'name_en' => 'right after meal', 'sort_order' => 3],
            ['code' => '05', 'name_th' => 'ก่อนอาหาร 30 นาที', 'name_en' => '30 mins before meal', 'sort_order' => 4],
            ['code' => '06', 'name_th' => 'หลังอาหาร 30 นาที', 'name_en' => '30 mins after meal', 'sort_order' => 5],
            ['code' => '07', 'name_th' => 'ขณะท้องว่าง', 'name_en' => 'on empty stomach', 'sort_order' => 6],
            ['code' => '08', 'name_th' => 'ไม่ต้องคำนึงถึงมื้ออาหาร', 'name_en' => 'with or without food', 'sort_order' => 7],
        ]);
    }
}
