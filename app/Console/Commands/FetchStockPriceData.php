<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockPrice;
use App\Services\StockApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchStockPriceData extends Command
{
    public function __construct(private StockApiService $stockApiService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-stock-price-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock price data from the API.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fetching stock price data...');

        Stock::all(['id', 'symbol'])
            ->each(function (Stock $stock) {
                $stockPriceData = $this->stockApiService->getStockPriceData($stock->symbol);

                if ($stockPriceData === null) {
                    $this->error("Failed to fetch stock price data for symbol: $stock->symbol");

                    return;
                }

                DB::transaction(function () use ($stockPriceData, $stock) {
                    collect($stockPriceData['Time Series (1min)'])
                        ->chunk(1000)
                        ->map(function ($chunk) use ($stock) {
                            return StockPrice::query()->upsert(
                                $chunk->map(function ($data, $dateTime) use ($stock) {
                                    return [
                                        'stock_id' => $stock->id,
                                        'price' => floatval($data['4. close']),
                                        'date_time' => $dateTime,
                                    ];
                                })->values()->all(),
                                ['stock_id', 'date_time'],
                                ['price']
                            );
                        });
                });

                $this->info("Stock price data fetched successfully for symbol: $stock->symbol");
            });

        return self::SUCCESS;
    }
}
