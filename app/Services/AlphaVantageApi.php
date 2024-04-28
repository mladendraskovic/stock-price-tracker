<?php

namespace App\Services;

use App\Contracts\StockApi;
use App\DTOs\StockPriceDTO;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Log\LogManager;

readonly class AlphaVantageApi implements StockApi
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
                    'interval' => '1min',
                    'outputsize' => 'full',
                    'apikey' => $this->config->get('services.alpha_vantage.api_key'),
                ]);

            $data = $response->json();

            if (! isset($data['Time Series (1min)'])) {
                $this->log->error('Invalid response data from the API.');

                return null;
            }

            return collect($data['Time Series (1min)'])
                ->map(fn ($data, $dateTime) => new StockPriceDTO(
                    symbol: $symbol,
                    price: floatval($data['4. close']),
                    dateTime: $dateTime,
                ))
                ->values()
                ->all();
        } catch (Exception $e) {
            $this->log->error('Failed to fetch stock price data.', [
                'symbol' => $symbol,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
