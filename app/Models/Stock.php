<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property string $name
 * @property string $symbol
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<StockPrice> $prices
 * @property StockPrice $latestPrice
 */
class Stock extends Model
{
    protected $fillable = [
        'name',
        'symbol',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(StockPrice::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(StockPrice::class)->latestOfMany('date_time');
    }
}
