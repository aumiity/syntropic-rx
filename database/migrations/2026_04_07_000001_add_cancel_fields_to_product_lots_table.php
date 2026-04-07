<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_lots', function (Blueprint $table) {
            if (!Schema::hasColumn('product_lots', 'is_cancelled')) {
                $table->boolean('is_cancelled')->default(false)->after('is_closed');
            }
            if (!Schema::hasColumn('product_lots', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('is_cancelled');
            }
            if (!Schema::hasColumn('product_lots', 'cancel_note')) {
                $table->text('cancel_note')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_lots', function (Blueprint $table) {
            $cols = array_filter(['is_cancelled', 'cancelled_at', 'cancel_note'], fn($c) => Schema::hasColumn('product_lots', $c));
            if (!empty($cols)) {
                $table->dropColumn(array_values($cols));
            }
        });
    }
};
