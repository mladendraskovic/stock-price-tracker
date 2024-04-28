<?php

use App\Console\Commands\FetchStockPriceData;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FetchStockPriceData::class)
    ->everyMinute()
    ->withoutOverlapping();
