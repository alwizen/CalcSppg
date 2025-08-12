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
    protected array $menuGroupIds;
    protected int $rowCount = 0; // cache jumlah baris data

    public function __construct($menuGroupIds)
    {
        $this->menuGroupIds = is_array($menuGroupIds) ? $menuGroupIds : explode(',', (string) $menuGroupIds);
    }

    public function array(): array
    {
        $data = [];

        $menuGroups = MenuGroup::with(['recipes.recipe.recipeIngredients.ingredient'])
            ->whereIn('id', $this->menuGroupIds)
            ->orderBy('date')
            ->get();

        foreach ($menuGroups as $menuGroup) {
            // Nama menu (gabungan)
            $menuNames = $menuGroup->recipes->map(fn($mr) => $mr->recipe->name)->join(', ');

            // Porsi global per group (fallback 1)
            $groupPortions = (int) ($menuGroup->requested_portions ?? 1);
            if ($groupPortions < 1) {
                $groupPortions = 1;
            }

            // Kumpulkan bahan per resep dengan multiplier dari porsi global
            $rows = [];
            foreach ($menuGroup->recipes as $menuRecipe) {
                $recipe = $menuRecipe->recipe;

                $base = (float) ($recipe->base_portions ?? 1);
                if ($base <= 0) {
                    $base = 1;
                }

                $multiplier = $groupPortions / $base;

                foreach ($recipe->recipeIngredients as $ri) {
                    $ingredient = $ri->ingredient;
                    $rows[] = [
                        'name'   => $ingredient->name ?? '-',
                        'amount' => round(((float) $ri->amount) * $multiplier, 2),
                        'unit'   => strtoupper($ingredient->unit ?? ''),
                    ];
                }
            }

            if (count($rows) > 0) {
                foreach ($rows as $index => $r) {
                    $data[] = [
                        $index === 0 ? $menuGroup->date->format('Y-m-d') : '', // Tanggal (hanya baris pertama)
                        $index === 0 ? $menuGroup->name : '',                  // Menu ke (hanya baris pertama)
                        $index === 0 ? $menuNames : '',                        // Nama Menu gabungan
                        $index === 0 ? $groupPortions : '',                    // Porsi (global)
                        $r['name'],                                            // Bahan-bahan
                        $r['amount'],                                          // Jumlah
                        $r['unit'],                                            // Satuan
                    ];
                }
            } else {
                // Tidak ada bahan
                $data[] = [
                    $menuGroup->date->format('Y-m-d'),
                    $menuGroup->name,
                    $menuNames,
                    $groupPortions,
                    'Tidak ada bahan',
                    '-',
                    '-',
                ];
            }
        }

        // simpan jumlah baris utk styling
        $this->rowCount = count($data);

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
            'Satuan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 10,
            'C' => 25,
            'D' => 8,
            'E' => 25,
            'F' => 12,
            'G' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // jika belum terisi (mis. urutan pemanggilan berbeda), fallback hitung cepat
        $dataCount = $this->rowCount > 0 ? $this->rowCount : count($this->array());

        return [
            // Header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Border seluruh data + header
            'A1:G' . ($dataCount + 1) => [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ],
        ];
    }
}
