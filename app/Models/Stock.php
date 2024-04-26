<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property string $symbol
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<StockPrice> $prices
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
}
