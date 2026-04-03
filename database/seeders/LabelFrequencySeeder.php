<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelFrequencySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_frequencies')->insertOrIgnore([
            ['code' => '01', 'name_th' => 'วันละ 1 ครั้ง', 'name_en' => 'Once daily'],
            ['code' => '02', 'name_th' => 'วันละ 2 ครั้ง', 'name_en' => 'Twice daily'],
            ['code' => '03', 'name_th' => 'วันละ 3 ครั้ง', 'name_en' => 'Three times daily'],
            ['code' => '04', 'name_th' => 'วันละ 4 ครั้ง', 'name_en' => 'Four times daily'],
            ['code' => '05', 'name_th' => 'วันละ 5 ครั้ง', 'name_en' => 'Five times daily'],
            ['code' => '06', 'name_th' => 'สัปดาห์ละ 1 ครั้ง', 'name_en' => 'Once weekly'],
            ['code' => '07', 'name_th' => 'เดือนละ 1 ครั้ง', 'name_en' => 'Once monthly'],
            ['code' => '08', 'name_th' => 'วันละ 2-3 ครั้ง', 'name_en' => '2-3 times daily'],
            ['code' => '09', 'name_th' => 'วันละ 3-4 ครั้ง', 'name_en' => '3-4 times daily'],
        ]);
    }
}
