<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Services\StockService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly ResponseFactory $response,
    ) {
    }

    public function index(): JsonResource
    {
        return StockResource::collection($this->stockService->getAllStocks());
    }

    public function multiple(Request $request): JsonResource
    {
        /** @var array<string> $symbols */
        $symbols = $request->validate([
            'symbols' => 'required|array|min:1',
            'symbols.*' => 'required|string|distinct|min:1|max:255',
        ])['symbols'];

        return StockResource::collection($this->stockService->getStocksBySymbols($symbols));
    }

    public function priceChangeReport(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $params */
        $params = $request->validate([
            'symbols' => 'required|array|min:1',
            'symbols.*' => 'required|string|distinct|min:1|max:255',
            'start_date_time' => 'required|date_format:Y-m-d H:i:s',
            'end_date_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        return $this->response->json($this->stockService->getStocksPriceChangeReport($params));
    }

    public function show(string $symbol): JsonResource
    {
        return new StockResource($this->stockService->getStockBySymbol($symbol));
    }
}
