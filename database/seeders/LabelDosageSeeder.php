<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelDosageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_dosages')->insertOrIgnore([
            ['name_th' => 'ทานครั้งละ 1 เม็ด', 'sort_order' => 0],
            ['name_th' => 'ทานครั้งละ 2 เม็ด', 'sort_order' => 1],
            ['name_th' => 'ทานครั้งละ 3 เม็ด', 'sort_order' => 2],
            ['name_th' => 'ทานครั้งละ 1/2 เม็ด', 'sort_order' => 3],
            ['name_th' => 'ทานครั้งละ 1 แคปซูล', 'sort_order' => 4],
            ['name_th' => 'ทานครั้งละ 2 แคปซูล', 'sort_order' => 5],
            ['name_th' => 'ทานครั้งละ 1 ช้อนชา (5 ml)', 'sort_order' => 6],
            ['name_th' => 'ทานครั้งละ 2 ช้อนชา (10 ml)', 'sort_order' => 7],
            ['name_th' => 'ทานครั้งละ 1 ช้อนโต๊ะ (15 ml)', 'sort_order' => 8],
            ['name_th' => 'ทานครั้งละ 5 ml', 'sort_order' => 9],
            ['name_th' => 'ทานครั้งละ 10 ml', 'sort_order' => 10],
        ]);
    }
}
