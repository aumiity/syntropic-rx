<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeCodes('customers', 'C');
        $this->normalizeCodes('suppliers', 'S');
    }

    public function down(): void
    {
        // Intentionally left blank. Code normalization is not safely reversible.
    }

    private function normalizeCodes(string $table, string $prefix, int $pad = 6): void
    {
        $rows = DB::table($table)
            ->select('id', 'code')
            ->orderBy('id')
            ->get();

        $max = 0;
        foreach ($rows as $row) {
            $code = (string) ($row->code ?? '');
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $code, $matches) === 1) {
                $num = (int) $matches[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        foreach ($rows as $row) {
            $code = (string) ($row->code ?? '');
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $code) === 1) {
                continue;
            }

            $max++;
            $newCode = $prefix . str_pad((string) $max, $pad, '0', STR_PAD_LEFT);

            DB::table($table)
                ->where('id', $row->id)
                ->update(['code' => $newCode]);
        }
    }
};
