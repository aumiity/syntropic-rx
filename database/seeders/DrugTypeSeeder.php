<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrugTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('drug_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('drug_types')->insert([
            ['code' => 'GENERAL',     'name_th' => 'ยาทั่วไป',                          'khor_yor_report' => null, 'is_disabled' => false],
            ['code' => 'OTC',         'name_th' => 'ยาสามัญประจำบ้าน',                   'khor_yor_report' => null, 'is_disabled' => false],
            ['code' => 'DANGEROUS',   'name_th' => 'ยาอันตราย',                          'khor_yor_report' => 9,    'is_disabled' => false],
            ['code' => 'SPCL_CTRL',   'name_th' => 'ยาควบคุมพิเศษ',                     'khor_yor_report' => 9,    'is_disabled' => false],
            ['code' => 'PSYCHO_3',    'name_th' => 'วัตถุออกฤทธิ์ประเภท 3',              'khor_yor_report' => 10,   'is_disabled' => false],
            ['code' => 'PSYCHO_4',    'name_th' => 'วัตถุออกฤทธิ์ประเภท 4',              'khor_yor_report' => 10,   'is_disabled' => false],
            ['code' => 'NARCOTIC_3',  'name_th' => 'ยาเสพติดให้โทษประเภท 3',             'khor_yor_report' => 12,   'is_disabled' => false],
            ['code' => 'TRADITIONAL', 'name_th' => 'ยาแผนโบราณ',                         'khor_yor_report' => null, 'is_disabled' => false],
            ['code' => 'EXTERNAL',    'name_th' => 'ยาอันตรายสำหรับใช้ภายนอก',            'khor_yor_report' => null, 'is_disabled' => false],
            ['code' => 'HERB',        'name_th' => 'สมุนไพร',                            'khor_yor_report' => null, 'is_disabled' => false],
        ]);
    }
}
