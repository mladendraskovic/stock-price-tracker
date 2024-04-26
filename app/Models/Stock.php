<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $name
 * @property string $symbol
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Stock extends Model
{
    protected $fillable = [
        'name',
        'symbol',
    ];
}
