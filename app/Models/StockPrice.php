<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $stock_id
 * @property float $price
 * @property Carbon $date_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Stock $stock
 */
class StockPrice extends Model
{
    protected $fillable = [
        'stock_id',
        'price',
        'date_time',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
