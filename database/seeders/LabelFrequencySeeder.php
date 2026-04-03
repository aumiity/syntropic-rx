<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelFrequencySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_frequencies')->insertOrIgnore([
            ['code' => '01', 'name_th' => 'วันละ 1 ครั้ง', 'name_en' => 'Once daily', 'sort_order' => 0],
            ['code' => '02', 'name_th' => 'วันละ 2 ครั้ง', 'name_en' => 'Twice daily', 'sort_order' => 1],
            ['code' => '03', 'name_th' => 'วันละ 3 ครั้ง', 'name_en' => 'Three times daily', 'sort_order' => 2],
            ['code' => '04', 'name_th' => 'วันละ 4 ครั้ง', 'name_en' => 'Four times daily', 'sort_order' => 3],
            ['code' => '05', 'name_th' => 'วันละ 5 ครั้ง', 'name_en' => 'Five times daily', 'sort_order' => 4],
            ['code' => '06', 'name_th' => 'สัปดาห์ละ 1 ครั้ง', 'name_en' => 'Once weekly', 'sort_order' => 5],
            ['code' => '07', 'name_th' => 'เดือนละ 1 ครั้ง', 'name_en' => 'Once monthly', 'sort_order' => 6],
            ['code' => '08', 'name_th' => 'วันละ 2-3 ครั้ง', 'name_en' => '2-3 times daily', 'sort_order' => 7],
            ['code' => '09', 'name_th' => 'วันละ 3-4 ครั้ง', 'name_en' => '3-4 times daily', 'sort_order' => 8],
        ]);
    }
}
