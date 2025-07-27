<?php

namespace App\Http\Controllers;

use App\Models\RecipeCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RecipeCalculationPdfController extends Controller
{
    public function generatePdf(RecipeCalculation $recipeCalculation)
    {
        // Load relasi yang diperlukan
        $recipeCalculation->load([
            'recipe',
            'calculatedIngredients.ingredient'
        ]);

        // Data untuk view
        $data = [
            'calculation' => $recipeCalculation,
            'recipe' => $recipeCalculation->recipe,
            'ingredients' => $recipeCalculation->calculatedIngredients,
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.recipe-calculation', $data);

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = 'perhitungan-resep-' . $recipeCalculation->recipe->slug . '-' . $recipeCalculation->requested_portions . '-porsi.pdf';

        // Return PDF download
        return $pdf->download($filename);
    }

    public function previewPdf(RecipeCalculation $recipeCalculation)
    {
        // Load relasi yang diperlukan
        $recipeCalculation->load([
            'recipe',
            'calculatedIngredients.ingredient'
        ]);

        // Data untuk view
        $data = [
            'calculation' => $recipeCalculation,
            'recipe' => $recipeCalculation->recipe,
            'ingredients' => $recipeCalculation->calculatedIngredients,
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];

        // Return view untuk preview (tanpa PDF)
        return view('pdf.recipe-calculation', $data);
    }
}
