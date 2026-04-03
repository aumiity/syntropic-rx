<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelTimeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_times')->insertOrIgnore([
            ['name_th' => 'เช้า', 'sort_order' => 0],
            ['name_th' => 'กลางวัน', 'sort_order' => 1],
            ['name_th' => 'เย็น', 'sort_order' => 2],
            ['name_th' => 'ก่อนนอน', 'sort_order' => 3],
            ['name_th' => 'เช้า-เย็น', 'sort_order' => 4],
            ['name_th' => 'เช้า-กลางวัน-เย็น', 'sort_order' => 5],
            ['name_th' => 'เช้า-กลางวัน-เย็น-ก่อนนอน', 'sort_order' => 6],
            ['name_th' => 'เมื่อมีอาการ', 'sort_order' => 7],
        ]);
    }
}
