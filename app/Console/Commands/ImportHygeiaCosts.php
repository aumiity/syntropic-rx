<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportHygeiaCosts extends Command
{
    protected $signature = 'import:hygeia-costs';

    protected $description = 'Import cost prices from Hygeia into products and product lots';

    public function handle(): int
    {
        $this->info('Starting cost price import from Hygeia...');

        try {
            $sourceTable = $this->resolveSourceTable();
            $targetConnection = DB::connection('mysql');
            $updated = 0;
            $skipped = 0;

            DB::connection('hygeia')
                ->table($sourceTable)
                ->select(['ItemKey', 'UnitPrice', 'MovAvgPrice'])
                ->whereNotNull('ItemKey')
                ->orderBy('ItemKey')
                ->chunk(1000, function ($items) use ($targetConnection, &$updated, &$skipped) {
                    $itemKeys = $items
                        ->pluck('ItemKey')
                        ->filter(static fn ($itemKey) => $itemKey !== null)
                        ->values();

                    if ($itemKeys->isEmpty()) {
                        return;
                    }

                    $products = $targetConnection
                        ->table('products')
                        ->select(['id', 'old_item_key'])
                        ->whereIn('old_item_key', $itemKeys)
                        ->get()
                        ->keyBy(static fn ($product) => (string) $product->old_item_key);

                    foreach ($items as $item) {
                        $product = $products->get((string) $item->ItemKey);

                        if (! $product) {
                            $skipped++;
                            continue;
                        }

                        $productUpdates = [];
                        $unitPrice = (float) $item->UnitPrice;
                        $movAvgPrice = (float) $item->MovAvgPrice;

                        if ($unitPrice > 0) {
                            $productUpdates['cost_price'] = round($unitPrice, 2);
                        }

                        if (empty($productUpdates) && $movAvgPrice <= 0) {
                            $skipped++;
                            continue;
                        }

                        $targetConnection->transaction(function () use ($targetConnection, $product, $productUpdates, $movAvgPrice) {
                            if (! empty($productUpdates)) {
                                $targetConnection
                                    ->table('products')
                                    ->where('id', $product->id)
                                    ->update($productUpdates);
                            }

                            if ($movAvgPrice > 0) {
                                $targetConnection
                                    ->table('product_lots')
                                    ->where('product_id', $product->id)
                                    ->update([
                                        'cost_price' => round($movAvgPrice, 2),
                                    ]);
                            }
                        });

                        $updated++;
                    }
                });

            $this->info("Updated {$updated} products, skipped {$skipped}");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Import failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveSourceTable(): string
    {
        if (Schema::connection('hygeia')->hasTable('item')) {
            return 'item';
        }

        if (Schema::connection('hygeia')->hasTable('Item')) {
            return 'Item';
        }

        throw new \RuntimeException('Hygeia item table was not found.');
    }
}