<?php

namespace App\DTOs;

readonly class StockPriceDTO
{
    public function __construct(
        public string $symbol,
        public float $price,
        public string $dateTime,
    ) {
    }
}
