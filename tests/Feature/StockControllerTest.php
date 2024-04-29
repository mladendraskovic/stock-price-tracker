<?php

namespace Tests\Feature;

use App\Models\Stock;
use Database\Factories\StockFactory;
use Database\Factories\StockPriceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_returns_all_stocks(): void
    {
        $stocks = StockFactory::new()
            ->count(2)
            ->has(StockPriceFactory::new()->count(3), 'prices')
            ->create();

        /** @var Stock $testStock */
        $testStock = $stocks->first();
        $latestStockPrice = $testStock->latestPrice;

        $this->getJson('/api/stocks')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'symbol',
                        'latest_price' => [
                            'price',
                            'date_time',
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $testStock->id,
                'name' => $testStock->name,
                'symbol' => $testStock->symbol,
                'latest_price' => [
                    'price' => $latestStockPrice->price,
                    'date_time' => $latestStockPrice->date_time->toIso8601String(),
                ],
            ]);
    }

    #[Test]
    public function it_returns_data_for_a_set_of_stocks(): void
    {
        $stocks = StockFactory::new()
            ->count(2)
            ->has(StockPriceFactory::new()->count(3), 'prices')
            ->create();

        /** @var Stock $testStock */
        $testStock = $stocks->first();
        $latestStockPrice = $testStock->latestPrice;

        $queryParams = Arr::query([
            'symbols' => [$testStock->symbol],
        ]);

        $this->getJson('/api/stocks/multiple?'.$queryParams)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'symbol',
                        'latest_price' => [
                            'price',
                            'date_time',
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $testStock->id,
                'name' => $testStock->name,
                'symbol' => $testStock->symbol,
                'latest_price' => [
                    'price' => $latestStockPrice->price,
                    'date_time' => $latestStockPrice->date_time->toIso8601String(),
                ],
            ]);
    }

    #[Test]
    public function it_returns_data_for_a_single_stocks(): void
    {
        $stocks = StockFactory::new()
            ->count(2)
            ->has(StockPriceFactory::new()->count(3), 'prices')
            ->create();

        /** @var Stock $testStock */
        $testStock = $stocks->first();
        $latestStockPrice = $testStock->latestPrice;

        $this->getJson('/api/stocks/'.$testStock->symbol)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'symbol',
                    'latest_price' => [
                        'price',
                        'date_time',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $testStock->id,
                'name' => $testStock->name,
                'symbol' => $testStock->symbol,
                'latest_price' => [
                    'price' => $latestStockPrice->price,
                    'date_time' => $latestStockPrice->date_time->toIso8601String(),
                ],
            ]);
    }

    #[Test]
    public function it_returns_price_change_report_data_for_a_set_of_stocks(): void
    {
        StockFactory::new()
            ->count(2)
            ->has(StockPriceFactory::new()->count(3), 'prices')
            ->create();

        /** @var Stock $testStock */
        $testStock = StockFactory::new()->create();

        StockPriceFactory::new()
            ->forStock($testStock)
            ->count(4)
            ->sequence(
                ['price' => 90, 'date_time' => '2024-01-01 12:00:00'],
                ['price' => 100, 'date_time' => '2024-01-02 09:15:00'],
                ['price' => 110, 'date_time' => '2024-01-03 18:30:00'],
                ['price' => 120, 'date_time' => '2024-01-04 12:00:00'],
            )
            ->create();

        $queryParams = Arr::query([
            'symbols' => [$testStock->symbol],
            'start_date_time' => '2024-01-02 00:00:00',
            'end_date_time' => '2024-01-03 23:59:59',
        ]);

        $this->getJson('/api/stocks/price-change-report?'.$queryParams)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'symbol',
                        'start_price',
                        'end_price',
                        'price_change',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'id' => $testStock->id,
                'name' => $testStock->name,
                'symbol' => $testStock->symbol,
                'start_price' => 100,
                'end_price' => 110,
                'price_change' => 10,
            ]);
    }
}
