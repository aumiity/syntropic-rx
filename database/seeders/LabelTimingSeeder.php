<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelTimingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('label_timings')->insertOrIgnore([
            ['code' => '01', 'name_th' => 'ก่อนอาหาร', 'name_en' => 'before a meal', 'name_mm' => null],
            ['code' => '02', 'name_th' => 'หลังอาหาร', 'name_en' => 'after a meal', 'name_mm' => null],
            ['code' => '03', 'name_th' => 'พร้อมอาหาร', 'name_en' => 'during a meal', 'name_mm' => null],
            ['code' => '04', 'name_th' => 'จิบเวลามีอาการ', 'name_en' => 'sip when symptom occurs', 'name_mm' => null],
            ['code' => '05', 'name_th' => 'หลังอาหารทันที', 'name_en' => 'right after a meal', 'name_mm' => null],
            ['code' => '06', 'name_th' => 'ทานทันทีเมื่อเริ่มมีอาการปวดหัว', 'name_en' => 'take right when headache', 'name_mm' => null],
            ['code' => '07', 'name_th' => 'ทานทันทีเมื่อเริ่มมีอาการ', 'name_en' => 'take right when symptom occurs', 'name_mm' => null],
            ['code' => '08', 'name_th' => 'ก่อนนอน', 'name_en' => 'before bed', 'name_mm' => null],
            ['code' => '09', 'name_th' => 'ขณะท้องว่าง', 'name_en' => 'take on an empty stomach', 'name_mm' => null],
            ['code' => '10', 'name_th' => 'หลังอาหาร 30 นาที', 'name_en' => '30 mins after a meal', 'name_mm' => null],
            ['code' => '11', 'name_th' => 'หลังอาหาร 1 ชั่วโมง', 'name_en' => '1 hr after a meal', 'name_mm' => null],
            ['code' => '12', 'name_th' => 'หลังอาหาร 2 ชั่วโมง', 'name_en' => '2 hrs after a meal', 'name_mm' => null],
            ['code' => '13', 'name_th' => 'ก่อนอาหาร 30 นาที', 'name_en' => '30 mins before a meal', 'name_mm' => null],
            ['code' => '14', 'name_th' => 'ก่อนอาหาร 1 ชั่วโมง', 'name_en' => '1 hr before a meal', 'name_mm' => null],
            ['code' => '15', 'name_th' => 'ก่อนอาหาร 2 ชั่วโมง', 'name_en' => '2 hrs before a meal', 'name_mm' => null],
            ['code' => '16', 'name_th' => 'ทานทันที', 'name_en' => 'PRN', 'name_mm' => null],
            ['code' => '17', 'name_th' => 'ไม่ต้องคำนึงถึงมื้ออาหาร', 'name_en' => 'with or without food', 'name_mm' => null],
        ]);
    }
}
