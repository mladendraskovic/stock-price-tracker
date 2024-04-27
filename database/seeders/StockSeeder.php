<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Stock::query()->exists()) {
            return;
        }

        $stocks = [
            [
                'name' => 'Alphabet Inc.',
                'symbol' => 'GOOGL',
            ],
            [
                'name' => 'Amazon.com Inc.',
                'symbol' => 'AMZN',
            ],
            [
                'name' => 'Apple Inc.',
                'symbol' => 'AAPL',
            ],
            [
                'name' => 'Meta Platforms Inc.',
                'symbol' => 'META',
            ],
            [
                'name' => 'Microsoft Corporation',
                'symbol' => 'MSFT',
            ],
        ];

        foreach ($stocks as $stock) {
            Stock::query()->create($stock);
        }
    }
}
