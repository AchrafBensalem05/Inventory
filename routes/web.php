<?php

use App\Http\Controllers\GeTurbineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/turbines', [GeTurbineController::class, 'index'])->name('turbines.index');
Route::post('/turbines/import', [GeTurbineController::class, 'import'])->name('turbines.import');
Route::get('/turbines/export', [GeTurbineController::class, 'export'])->name('turbines.export');
Route::get('/turbines/{turbine}/edit', [GeTurbineController::class, 'edit'])->name('turbines.edit');
Route::put('/turbines/{turbine}', [GeTurbineController::class, 'update'])->name('turbines.update');
