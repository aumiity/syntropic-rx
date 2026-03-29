<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'barcode' => '1000000000001', 'code' => 'PARA500',
                'trade_name' => 'Paracetamol 500mg', 'name_for_print' => 'พาราเซตามอล 500mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 5.00, 'price_wholesale1' => 3.00,
                'drug_type_id' => 1, 'strength' => 500,
                'reorder_point' => 50, 'safety_stock' => 20,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'บรรเทาปวด ลดไข้',
            ],
            [
                'barcode' => '1000000000002', 'code' => 'IBU400',
                'trade_name' => 'Ibuprofen 400mg', 'name_for_print' => 'ไอบูโพรเฟน 400mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 12.00, 'price_wholesale1' => 8.00,
                'drug_type_id' => 2, 'strength' => 400,
                'reorder_point' => 30, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'บรรเทาปวด ลดการอักเสบ',
            ],
            [
                'barcode' => '1000000000003', 'code' => 'AMOX500',
                'trade_name' => 'Amoxicillin 500mg', 'name_for_print' => 'อะม็อกซีซิลลิน 500mg',
                'item_type' => 'drug', 'dosage_form_id' => 2, 'unit_id' => 1,
                'price_retail' => 18.00, 'price_wholesale1' => 12.00,
                'drug_type_id' => 2, 'strength' => 500,
                'reorder_point' => 20, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => true, 'is_fda_report' => false,
                'indication_note' => 'ยาปฏิชีวนะกลุ่ม Penicillin',
            ],
            [
                'barcode' => '1000000000004', 'code' => 'OMEP20',
                'trade_name' => 'Omeprazole 20mg', 'name_for_print' => 'โอมีพราโซล 20mg',
                'item_type' => 'drug', 'dosage_form_id' => 2, 'unit_id' => 1,
                'price_retail' => 25.00, 'price_wholesale1' => 15.00,
                'drug_type_id' => 2, 'strength' => 20,
                'reorder_point' => 20, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'ลดกรดในกระเพาะอาหาร',
            ],
            [
                'barcode' => '1000000000005', 'code' => 'LORA10',
                'trade_name' => 'Loratadine 10mg', 'name_for_print' => 'โลราทาดีน 10mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 8.00, 'price_wholesale1' => 5.00,
                'drug_type_id' => 1, 'strength' => 10,
                'reorder_point' => 30, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'แก้แพ้ ลดน้ำมูก',
            ],
            [
                'barcode' => '1000000000006', 'code' => 'METF500',
                'trade_name' => 'Metformin 500mg', 'name_for_print' => 'เมทฟอร์มิน 500mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 4.00, 'price_wholesale1' => 2.50,
                'drug_type_id' => 2, 'strength' => 500,
                'reorder_point' => 50, 'safety_stock' => 20,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'ยารักษาเบาหวานชนิดที่ 2',
            ],
            [
                'barcode' => '1000000000007', 'code' => 'ATOR20',
                'trade_name' => 'Atorvastatin 20mg', 'name_for_print' => 'อะทอร์วาสแตติน 20mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 35.00, 'price_wholesale1' => 22.00,
                'drug_type_id' => 2, 'strength' => 20,
                'reorder_point' => 20, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'ลดไขมันในเลือด',
            ],
            [
                'barcode' => '1000000000008', 'code' => 'VITC1000',
                'trade_name' => 'Vitamin C 1000mg', 'name_for_print' => 'วิตามินซี 1000mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 12.00, 'price_wholesale1' => 8.00,
                'drug_type_id' => 1, 'strength' => 1000,
                'reorder_point' => 30, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'เสริมภูมิคุ้มกัน',
            ],
            [
                'barcode' => '1000000000009', 'code' => 'CETI10',
                'trade_name' => 'Cetirizine 10mg', 'name_for_print' => 'เซทิริซีน 10mg',
                'item_type' => 'drug', 'dosage_form_id' => 1, 'unit_id' => 1,
                'price_retail' => 10.00, 'price_wholesale1' => 6.00,
                'drug_type_id' => 1, 'strength' => 10,
                'reorder_point' => 30, 'safety_stock' => 10,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'แก้แพ้ ลดอาการคัน',
            ],
            [
                'barcode' => '1000000000010', 'code' => 'ANTACID',
                'trade_name' => 'Antacid Suspension 180ml', 'name_for_print' => 'ยาลดกรด น้ำแขวนตะกอน',
                'item_type' => 'drug', 'dosage_form_id' => 3, 'unit_id' => 4,
                'price_retail' => 55.00, 'price_wholesale1' => 35.00,
                'drug_type_id' => 1, 'strength' => null,
                'reorder_point' => 10, 'safety_stock' => 5,
                'expiry_alert_days1' => 90, 'expiry_alert_days2' => 60, 'expiry_alert_days3' => 30,
                'is_antibiotic' => false, 'is_fda_report' => false,
                'indication_note' => 'บรรเทาอาการแสบร้อนกลางอก กรดไหลย้อน',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['barcode' => $product['barcode']],
                array_merge($product, [
                    'is_disabled' => false,
                    'is_hidden' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
