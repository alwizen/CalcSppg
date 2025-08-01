<?php

namespace App\Http\Controllers;

use App\Exports\MenuGroupBulkExport;
use App\Models\MenuGroup;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MenuGroupController extends Controller
{
    // Method yang sudah ada...

    public function bulkExport(Request $request)
    {
        $ids = explode(',', $request->get('ids'));
        $filename = 'menu_groups_bulk_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new MenuGroupBulkExport($ids), $filename);
    }

    public function bulkPrint(Request $request)
    {
        $ids = explode(',', $request->get('ids'));

        $menuGroups = MenuGroup::with(['recipes.recipe.recipeIngredients.ingredient'])
            ->whereIn('id', $ids)
            ->orderBy('date')
            ->get();

        // Prepare data sama seperti di export
        $data = [];
        foreach ($menuGroups as $menuGroup) {
            // Gabungkan semua nama menu dengan koma
            $menuNames = $menuGroup->recipes->map(function ($menuRecipe) {
                return $menuRecipe->recipe->name;
            })->join(', ');

            // Ambil porsi dari menu pertama (karena input sama untuk semua)
            $totalPortions = $menuGroup->recipes->first()?->requested_portions ?? 0;

            // Kumpulkan semua ingredients dari semua recipes dalam menu group ini
            $allIngredients = collect();

            foreach ($menuGroup->recipes as $menuRecipe) {
                $recipe = $menuRecipe->recipe;
                $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;
                $ingredients = $recipe->recipeIngredients;

                foreach ($ingredients as $ri) {
                    $ingredient = $ri->ingredient;
                    $amount = round($ri->amount * $multiplier, 2);

                    $allIngredients->push([
                        'name' => $ingredient->name,
                        'amount' => $amount,
                        'unit' => $ingredient->unit
                    ]);
                }
            }

            $data[] = [
                'menu_group' => $menuGroup,
                'menu_names' => $menuNames,
                'total_portions' => $totalPortions,
                'ingredients' => $allIngredients
            ];
        }

        $pdf = Pdf::loadView('pdf.menu-groups-bulk', compact('data'));
        $filename = 'menu_groups_bulk_print_' . now()->format('Y_m_d_H_i_s') . '.pdf';

        return $pdf->download($filename);
    }
}
