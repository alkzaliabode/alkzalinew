<?php

use App\Models\DailyStatus;
use Illuminate\Support\Facades\Route;


Route::get('/print-daily-status/{record}', function (DailyStatus $record) {
    return view('print.daily-status', compact('record'));
})->name('print.daily-status');

Route::get('/', function () {
    return view('welcome');
});

