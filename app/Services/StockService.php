<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

readonly class StockService
{
    private const INTERVAL_IN_MINUTES = 1;

    public function __construct(
        private Repository $cache,
        private Connection $db,
    ) {
    }

    public function getAllStocks(): Collection
    {
        return $this->cache->remember(
            'stocks',
            now()->addMinutes(self::INTERVAL_IN_MINUTES),
            fn () => Stock::with('latestPrice')->get(),
        );
    }

    public function getStockBySymbol(string $symbol): Stock
    {
        return $this->cache->remember(
            "stock-$symbol",
            now()->addMinutes(self::INTERVAL_IN_MINUTES),
            fn () => Stock::query()
                ->with('latestPrice')
                ->where('symbol', $symbol)
                ->firstOrFail(),
        );
    }

    public function getStocksBySymbols(array $symbols): Collection
    {
        return $this->cache->remember(
            'stocks-'.implode('-', $symbols),
            now()->addMinutes(self::INTERVAL_IN_MINUTES),
            fn () => Stock::with('latestPrice')
                ->whereIn('symbol', $symbols)
                ->get(),
        );
    }

    public function getStocksPriceChangeReport(array $params)
    {
        $symbols = $params['symbols'];
        $startDate = $params['start_date_time'];
        $endDate = $params['end_date_time'];

        return $this->db->table('stocks')
            ->select([
                'id',
                'name',
                'symbol',
            ])
            ->addSelect([
                'start_price' => fn (Builder $query) => $query
                    ->select('price')
                    ->from('stock_prices')
                    ->whereColumn('stock_id', 'stocks.id')
                    ->whereBetween('date_time', [$startDate, $endDate])
                    ->orderBy('date_time')
                    ->limit(1),
                'end_price' => fn (Builder $query) => $query
                    ->select('price')
                    ->from('stock_prices')
                    ->whereColumn('stock_id', 'stocks.id')
                    ->whereBetween('date_time', [$startDate, $endDate])
                    ->orderByDesc('date_time')
                    ->limit(1),
            ])
            ->whereIn('symbol', $symbols)
            ->get()
            ->map(function (object $stock) {
                $startPrice = $stock->start_price ? floatval($stock->start_price) : null;
                $endPrice = $stock->end_price ? floatval($stock->end_price) : null;

                $priceChange = isset($startPrice, $endPrice) ?
                    ($endPrice - $startPrice) / $startPrice * 100
                    : null;

                return [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'symbol' => $stock->symbol,
                    'start_price' => $startPrice,
                    'end_price' => $endPrice,
                    'price_change' => $priceChange ? round($priceChange, 2) : null,
                ];
            })
            ->all();
    }
}
