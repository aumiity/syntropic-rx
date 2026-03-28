<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('old_customer_key')->nullable();
            $table->string('code', 20)->unique()->nullable();
            $table->string('full_name', 150);
            $table->string('id_card', 20)->nullable();
            $table->string('hn', 30)->nullable();
            $table->date('dob')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->boolean('hc_uc')->default(false);
            $table->boolean('hc_gov')->default(false);
            $table->boolean('hc_sso')->default(false);
            $table->text('food_allergy')->nullable();
            $table->text('other_allergy')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->boolean('is_alert')->default(false);
            $table->text('alert_note')->nullable();
            $table->text('warning_note')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        // เพิ่มลูกค้าทั่วไป id=1
        DB::table('customers')->insert([
            'id'        => 1,
            'full_name' => 'ลูกค้าทั่วไป',
            'code'      => 'GENERAL',
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        // drug_types
        Schema::create('drug_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name_th', 100);
            $table->tinyInteger('khor_yor_report')->nullable();
            $table->boolean('is_disabled')->default(false);
        });

        DB::table('drug_types')->insert([
            ['code' => 'GENERAL',    'name_th' => 'ยาทั่วไป',             'khor_yor_report' => null],
            ['code' => 'DANGEROUS',  'name_th' => 'ยาอันตราย',            'khor_yor_report' => null],
            ['code' => 'SPCL_CTRL',  'name_th' => 'ยาควบคุมพิเศษ',        'khor_yor_report' => 9],
            ['code' => 'PSYCHO_4',   'name_th' => 'วัตถุออกฤทธิ์ ประเภท 4','khor_yor_report' => 10],
            ['code' => 'NARCOTIC_3', 'name_th' => 'ยาเสพติด ประเภท 3',    'khor_yor_report' => 12],
            ['code' => 'OTC',        'name_th' => 'ยาสามัญประจำบ้าน',      'khor_yor_report' => null],
        ]);

        // dosage_forms
        Schema::create('dosage_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name_th', 100);
            $table->string('name_en', 100)->nullable();
            $table->boolean('is_disabled')->default(false);
        });

        DB::table('dosage_forms')->insert([
            ['name_th' => 'เม็ด',      'name_en' => 'Tablet'],
            ['name_th' => 'แคปซูล',    'name_en' => 'Capsule'],
            ['name_th' => 'น้ำเชื่อม', 'name_en' => 'Syrup'],
            ['name_th' => 'ครีม',      'name_en' => 'Cream'],
            ['name_th' => 'ยาฉีด',     'name_en' => 'Injection'],
            ['name_th' => 'ยาพ่น',     'name_en' => 'Inhaler'],
        ]);

        // item_units
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('multiply', 10, 4)->default(1);
        });

        DB::table('item_units')->insert([
            ['name' => 'เม็ด',  'multiply' => 1],
            ['name' => 'แผง',   'multiply' => 10],
            ['name' => 'กล่อง', 'multiply' => 100],
            ['name' => 'ขวด',   'multiply' => 1],
            ['name' => 'หลอด',  'multiply' => 1],
            ['name' => 'ซอง',   'multiply' => 1],
        ]);

        // suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->integer('old_vendor_key')->nullable();
            $table->string('code', 20)->unique()->nullable();
            $table->string('name', 200);
            $table->string('tax_id', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();
        });

        // products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('old_item_key')->nullable()->index();
            $table->string('barcode', 50)->unique()->nullable();
            $table->string('barcode2', 50)->nullable();
            $table->string('code', 30)->unique()->nullable();
            $table->string('trade_name', 200);
            $table->string('name_for_print', 200)->nullable();
            $table->enum('item_type', ['drug','supply','equipment','service'])->default('drug');
            $table->foreignId('dosage_form_id')->nullable()->constrained('dosage_forms');
            $table->foreignId('unit_id')->nullable()->constrained('item_units');
            $table->boolean('is_stock_item')->default(true);
            $table->decimal('price_retail', 10, 2)->default(0);
            $table->decimal('price_wholesale1', 10, 2)->default(0);
            $table->decimal('price_wholesale2', 10, 2)->default(0);
            $table->boolean('is_vat')->default(false);
            $table->boolean('is_not_discount')->default(false);
            $table->integer('reorder_point')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('expiry_alert_days1')->default(90);
            $table->integer('expiry_alert_days2')->default(60);
            $table->integer('expiry_alert_days3')->default(30);
            // drug specific
            $table->foreignId('drug_type_id')->nullable()->constrained('drug_types');
            $table->decimal('strength', 10, 4)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->string('tmt_id', 30)->nullable();
            $table->boolean('is_original_drug')->default(false);
            $table->boolean('is_antibiotic')->default(false);
            $table->decimal('max_dispense_qty', 10, 2)->nullable();
            $table->text('indication_note')->nullable();
            $table->text('side_effect_note')->nullable();
            $table->boolean('is_fda_report')->default(false);
            $table->boolean('is_fda13_report')->default(false);
            $table->boolean('is_sale_control')->default(false);
            $table->decimal('sale_control_qty', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();

            $table->index('trade_name');
            $table->index('drug_type_id');
        });

        // product_lots
        Schema::create('product_lots', function (Blueprint $table) {
            $table->id();
            $table->integer('old_lot_key')->nullable();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->string('lot_number', 100);
            $table->date('manufactured_date')->nullable();
            $table->date('expiry_date');
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sell_price', 10, 2)->default(0);
            $table->integer('qty_received')->default(0);
            $table->integer('qty_on_hand')->default(0);
            $table->integer('qty_reserved')->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'lot_number']);
            $table->index('expiry_date');
        });

        // sales
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('old_sale_key')->nullable();
            $table->string('invoice_no', 30)->unique();
            $table->enum('sale_type', ['retail','wholesale','rx','return'])->default('retail');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('customer_name_free', 150)->nullable();
            $table->foreignId('sold_by')->constrained('users');
            $table->timestamp('sold_at')->useCurrent();
            $table->string('age_range', 20)->nullable();
            $table->text('symptom_note')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_discount', 12, 2)->default(0);
            $table->decimal('total_vat', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('cash_amount', 12, 2)->default(0);
            $table->decimal('card_amount', 12, 2)->default(0);
            $table->decimal('transfer_amount', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->boolean('is_credit')->default(false);
            $table->date('due_date')->nullable();
            $table->boolean('is_fda13_report')->default(false);
            $table->string('sale_report_note', 200)->nullable();
            $table->enum('status', ['completed','voided','refunded'])->default('completed');
            $table->text('void_reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('sold_at');
        });

        // sale_items
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->string('item_name', 200)->nullable();
            $table->string('unit_name', 50)->nullable();
            $table->decimal('qty', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('unit_vat', 10, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->text('item_note')->nullable();
            $table->boolean('is_cancelled')->default(false);
        });

        // sale_item_lots (FEFO tracking)
        Schema::create('sale_item_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_item_id')->constrained('sale_items')->onDelete('cascade');
            $table->foreignId('lot_id')->constrained('product_lots');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('qty', 10, 2);
            $table->boolean('is_cancelled')->default(false);
        });

        // stock_movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('lot_id')->constrained('product_lots');
            $table->enum('movement_type', [
                'receive','sale','sale_return','purchase_return',
                'adjust_in','adjust_out','expired','transfer_in','transfer_out','destroy'
            ]);
            $table->string('ref_type', 30)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->integer('qty_change');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'lot_id']);
            $table->index('movement_type');
            $table->index('created_at');
        });

        // drug_allergies
        Schema::create('drug_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->unsignedBigInteger('generic_name_id')->nullable();
            $table->string('drug_name_free', 200)->nullable();
            $table->text('reaction');
            $table->enum('severity', ['mild','moderate','severe','life_threatening'])->default('moderate');
            $table->tinyInteger('naranjo_score')->nullable();
            $table->foreignId('noted_by')->nullable()->constrained('users');
            $table->timestamp('noted_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_allergies');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('sale_item_lots');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('product_lots');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('dosage_forms');
        Schema::dropIfExists('drug_types');
        Schema::dropIfExists('customers');
    }
};
