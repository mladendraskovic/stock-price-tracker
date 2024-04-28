<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;

readonly class StockService
{
    private const INTERVAL_IN_MINUTES = 1;

    public function __construct(private Repository $cache)
    {
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
}
