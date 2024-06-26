<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockPrice>
 */
class StockPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => StockFactory::new(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'date_time' => $this->faker->dateTime(),
        ];
    }

    public function forStock(Stock $stock): self
    {
        return $this->state(fn (array $attributes) => [
            'stock_id' => $stock->id,
        ]);
    }
}
