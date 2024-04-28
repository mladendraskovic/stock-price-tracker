<?php

namespace App\Contracts;

use App\DTOs\StockPriceDTO;

interface StockApi
{
    /**
     * @return array<StockPriceDTO>|null
     */
    public function getStockPriceData(string $symbol): ?array;
}
