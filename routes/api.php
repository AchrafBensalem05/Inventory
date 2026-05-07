<?php

use Illuminate\Support\Facades\Route;

Route::get('/inventory/summary', function () {
    return response()->json([
        'categories' => ['Pipes', 'Valves', 'Chemicals', 'PPE'],
        'quantities' => [120, 45, 300, 80],
        'thresholds' => [100, 60, 250, 90],
    ]);
});
