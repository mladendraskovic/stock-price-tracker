<?php

namespace App\Services;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Log\LogManager;

readonly class StockApiService
{
    public function __construct(
        private Repository $config,
        private Factory $http,
        private LogManager $log,
    ) {
    }

    public function getStockPriceData(string $symbol): ?array
    {
        try {
            $response = $this->http
                ->throw()
                ->get('https://www.alphavantage.co/query', [
                    'function' => 'TIME_SERIES_INTRADAY',
                    'symbol' => $symbol,
                    'interval' => '5min',
                    'apikey' => $this->config->get('services.alpha_vantage.api_key'),
                ]);

            return $response->json();
        } catch (Exception $e) {
            $this->log->error($e->getMessage());

            return null;
        }
    }
}