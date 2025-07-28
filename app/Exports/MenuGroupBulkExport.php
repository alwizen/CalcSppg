<?php

namespace App\Exports;

use App\Models\MenuGroup;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MenuGroupBulkExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $menuGroupIds;

    public function __construct($menuGroupIds)
    {
        $this->menuGroupIds = $menuGroupIds;
    }

    public function array(): array
    {
        $data = [];

        $menuGroups = MenuGroup::with(['recipes.recipe.recipeIngredients.ingredient'])
            ->whereIn('id', $this->menuGroupIds)
            ->orderBy('date')
            ->get();

        foreach ($menuGroups as $menuGroup) {
            // Gabungkan semua nama menu dengan koma
            $menuNames = $menuGroup->recipes->map(function ($menuRecipe) {
                return $menuRecipe->recipe->name;
            })->join(', ');

            // Gabungkan semua porsi
            $totalPortions = $menuGroup->recipes->sum('requested_portions');

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

            // Tambahkan data untuk setiap ingredient
            if ($allIngredients->count() > 0) {
                foreach ($allIngredients as $index => $ingredient) {
                    $data[] = [
                        $index === 0 ? $menuGroup->date->format('Y-m-d') : '',  // Tanggal (hanya di row pertama)
                        $index === 0 ? $menuGroup->name : '',                   // Menu ke (hanya di row pertama)
                        $index === 0 ? $menuNames : '',                         // Nama Menu gabungan (hanya di row pertama)
                        $index === 0 ? $totalPortions : '',                     // Total Porsi (hanya di row pertama)
                        $ingredient['name'],                                     // Bahan-bahan
                        $ingredient['amount'],                                   // Jumlah
                        $ingredient['unit'],                                     // Satuan
                    ];
                }
            } else {
                // If no ingredients
                $data[] = [
                    $menuGroup->date->format('Y-m-d'),
                    $menuGroup->name,
                    $menuNames,
                    $totalPortions,
                    'Tidak ada bahan',
                    '-',
                    '-'
                ];
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Menu ke',
            'Nama Menu',
            'Porsi',
            'Bahan-bahan',
            'Jumlah',
            'Satuan'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 10,
            'C' => 25,
            'D' => 8,
            'E' => 20,
            'F' => 10,
            'G' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->array());

        return [
            // Header styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // All borders
            'A1:G' . ($dataCount + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]
        ];
    }
}
