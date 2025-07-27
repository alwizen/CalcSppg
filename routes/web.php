<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\RecipeCalculationPdfController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/recipe-calculation/{recipeCalculation}/pdf', [RecipeCalculationPdfController::class, 'generatePdf'])
        ->name('recipe-calculation.pdf');

    Route::get('/recipe-calculation/{recipeCalculation}/preview', [RecipeCalculationPdfController::class, 'previewPdf'])
        ->name('recipe-calculation.preview');
});


Route::get('/', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
