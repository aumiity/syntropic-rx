<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportHygieaProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:hygeia-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Hygeia database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting import of products from Hygeia...');

        try {
            $now = now();
            $inserted = 0;
            $totalRead = 0;

            DB::connection('hygeia')
                ->table('Item')
                ->whereNotNull('Name')
                ->orderBy('ItemKey')
                ->chunk(1000, function ($items) use (&$inserted, &$totalRead, $now) {
                    $rows = [];

                    foreach ($items as $item) {
                        $totalRead++;

                        $rows[] = [
                            'old_item_key' => $item->ItemKey,
                            'code' => $item->Code,
                            'trade_name' => $item->Name,
                            'name_for_print' => $item->NameForPrint,
                            'search_keywords' => $item->OtherName,
                            'barcode' => $item->BarCode,
                            'barcode2' => $item->BarCode2,
                            'barcode3' => $item->BarCode3,
                            'barcode4' => $item->BarCode4,
                            'note' => $item->Note,
                            'is_vat' => (bool) $item->IsTax,
                            'default_qty' => (int) ($item->SaleDefQty ?? 0),
                            'price_retail' => $item->SalePrice,
                            'price_wholesale1' => $item->Wholesale1,
                            'price_wholesale2' => $item->Wholesale2,
                            'reorder_point' => (int) ($item->ReorderPoint ?? 0),
                            'is_disabled' => (bool) $item->IsDisabled,
                            'is_hidden' => (bool) $item->IsHidden,
                            'is_stock_item' => (bool) $item->IsStockItem,
                            'is_not_discount' => (bool) $item->IsNotDiscount,
                            'tmt_id' => $item->TMTID,
                            'is_sale_control' => (bool) $item->IsSaleControl,
                            'sale_control_qty' => $item->SaleControlQty,
                            'expiry_alert_days1' => $item->ExpDayNum1,
                            'expiry_alert_days2' => $item->ExpDayNum2,
                            'expiry_alert_days3' => $item->ExpDayNum3,
                            'unit_name' => $item->SaleUnitName,
                            'drug_type_id' => null,
                            'category_id' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (!empty($rows)) {
                        $result = DB::table('products')->insertOrIgnore($rows);
                        $inserted += (int) $result;
                    }
                });

            $ignored = $totalRead - $inserted;

            $this->info("Import completed!");
            $this->info("Read: {$totalRead} products");
            $this->info("Inserted: {$inserted} products");
            $this->info("Ignored: {$ignored} products");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
