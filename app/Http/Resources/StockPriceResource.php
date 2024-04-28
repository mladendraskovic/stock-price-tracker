<?php

namespace App\Http\Resources;

use App\Models\StockPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StockPrice
 */
class StockPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'price' => $this->price,
            'date_time' => $this->date_time->toIso8601String(),
        ];
    }
}
