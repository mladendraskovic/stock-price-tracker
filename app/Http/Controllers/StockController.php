<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockController extends Controller
{
    public function __construct(private StockService $stockService)
    {
    }

    public function index(): JsonResource
    {
        return StockResource::collection($this->stockService->getAllStocks());
    }

    public function multiple(Request $request): JsonResource
    {
        $symbols = $request->validate([
            'symbols' => 'required|array|min:1',
            'symbols.*' => 'required|string|distinct|min:1|max:255',
        ])['symbols'];

        return StockResource::collection($this->stockService->getStocksBySymbols($symbols));
    }

    public function show(string $symbol): JsonResource
    {
        return new StockResource($this->stockService->getStockBySymbol($symbol));
    }
}
