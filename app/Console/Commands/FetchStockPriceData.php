<?php

namespace App\Console\Commands;

use App\Contracts\StockApi;
use App\DTOs\StockPriceDTO;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Throwable;

class FetchStockPriceData extends Command
{
    public function __construct(
        private readonly StockApi $stockApi,
        private readonly Connection $db,
    ) {
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
     *
     * @throws Throwable
     */
    public function handle(): int
    {
        $this->info('Fetching stock price data...');

        Stock::all(['id', 'symbol'])
            ->each(function (Stock $stock) {
                $stockPriceData = $this->stockApi->getStockPriceData($stock->symbol);

                if ($stockPriceData === null) {
                    $this->error("Failed to fetch stock price data for symbol: $stock->symbol");

                    return;
                }

                $this->db->transaction(fn () => $this->persistStockPriceData($stockPriceData, $stock));

                $this->info("Stock price data saved successfully for symbol: $stock->symbol");
            });

        return self::SUCCESS;
    }

    /**
     * @param  array<StockPriceDTO>  $stockPriceData
     */
    private function persistStockPriceData(array $stockPriceData, Stock $stock): void
    {
        collect($stockPriceData)
            ->chunk(1000)
            ->each(function (Collection $chunk) use ($stock) {
                $data = $chunk
                    ->map(fn (StockPriceDTO $stockPriceDTO) => [
                        'stock_id' => $stock->id,
                        'price' => $stockPriceDTO->price,
                        'date_time' => $stockPriceDTO->dateTime,
                    ])
                    ->values()
                    ->all();

                StockPrice::query()->upsert(
                    $data,
                    ['stock_id', 'date_time'],
                    ['price']
                );
            });
    }
}
