<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_lots')) {
            return;
        }

        $hasSupplierId = Schema::hasColumn('product_lots', 'supplier_id');
        $hasInvoiceNo = Schema::hasColumn('product_lots', 'invoice_no');
        $hasSupplierInvoiceNo = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasPaymentType = Schema::hasColumn('product_lots', 'payment_type');
        $hasDueDate = Schema::hasColumn('product_lots', 'due_date');
        $hasIsPaid = Schema::hasColumn('product_lots', 'is_paid');
        $hasPaidDate = Schema::hasColumn('product_lots', 'paid_date');
        $hasQtyReceived = Schema::hasColumn('product_lots', 'qty_received');

        Schema::table('product_lots', function (Blueprint $table) use (
            $hasSupplierId,
            $hasInvoiceNo,
            $hasSupplierInvoiceNo,
            $hasPaymentType,
            $hasDueDate,
            $hasIsPaid,
            $hasPaidDate,
            $hasQtyReceived
        ) {
            if (! $hasSupplierId) {
                $table->foreignId('supplier_id')->nullable()->after('product_id')->constrained('suppliers');
            }

            if (! $hasInvoiceNo) {
                $table->string('invoice_no', 100)->nullable()->after('supplier_id');
            }

            if (! $hasSupplierInvoiceNo) {
                $table->string('supplier_invoice_no', 100)->nullable()->after('invoice_no');
            }

            if (! $hasPaymentType) {
                $table->enum('payment_type', ['cash', 'credit'])->default('cash')->after('supplier_invoice_no');
            }

            if (! $hasDueDate) {
                $table->date('due_date')->nullable()->after('payment_type');
            }

            if (! $hasIsPaid) {
                $table->boolean('is_paid')->default(false)->after('due_date');
            }

            if (! $hasPaidDate) {
                $table->date('paid_date')->nullable()->after('is_paid');
            }

            if (! $hasQtyReceived) {
                $table->integer('qty_received')->default(0)->after('sell_price');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_lots')) {
            return;
        }

        $hasInvoiceNo = Schema::hasColumn('product_lots', 'invoice_no');
        $hasSupplierInvoiceNo = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasPaymentType = Schema::hasColumn('product_lots', 'payment_type');
        $hasDueDate = Schema::hasColumn('product_lots', 'due_date');
        $hasIsPaid = Schema::hasColumn('product_lots', 'is_paid');
        $hasPaidDate = Schema::hasColumn('product_lots', 'paid_date');

        Schema::table('product_lots', function (Blueprint $table) use (
            $hasInvoiceNo,
            $hasSupplierInvoiceNo,
            $hasPaymentType,
            $hasDueDate,
            $hasIsPaid,
            $hasPaidDate
        ) {
            $columnsToDrop = array_values(array_filter([
                $hasInvoiceNo ? 'invoice_no' : null,
                $hasSupplierInvoiceNo ? 'supplier_invoice_no' : null,
                $hasPaymentType ? 'payment_type' : null,
                $hasDueDate ? 'due_date' : null,
                $hasIsPaid ? 'is_paid' : null,
                $hasPaidDate ? 'paid_date' : null,
            ]));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
