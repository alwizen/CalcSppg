<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Menu Groups</title>
    <style>
        /* Aman untuk dompdf */
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; margin: 20px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 4px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #4472C4; color: #fff; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .page-break { page-break-before: always; }
        .menu-group-separator { border-top: 2px solid #000; }
    </style>
</head>
<body>
@php
    $fmt = fn($n) => number_format((float)$n, 2, ',', '.');
@endphp

<div class="header">
    <h1>LAPORAN KEBUTUHAN BAHAN DAPUR</h1>
    <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th width="12%">Tanggal</th>
            <th width="10%">Menu ke</th>
            <th width="25%">Nama Menu</th>
            <th width="8%">Porsi</th>
            <th width="25%">Bahan-bahan</th>
            <th width="10%">Jumlah</th>
            <th width="10%">Satuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $gIndex => $menuData)
            @php
                // Ambil model lengkap; abaikan 'ingredients' lama agar pasti pakai logika porsi global
                /** @var \App\Models\MenuGroup $group */
                $group = $menuData['menu_group']->load('recipes.recipe.recipeIngredients.ingredient');

                $groupPortions = (int) ($group->requested_portions ?? 1);
                if ($groupPortions < 1) $groupPortions = 1;

                $menuNames = $group->recipes->map(fn($mr) => $mr->recipe->name)->join(', ');

                // Build rows bahan utk group ini
                $rows = [];
                foreach ($group->recipes as $menuRecipe) {
                    $recipe = $menuRecipe->recipe;
                    $base = (float) ($recipe->base_portions ?? 1);
                    if ($base <= 0) $base = 1;

                    $multiplier = $groupPortions / $base;

                    foreach ($recipe->recipeIngredients as $ri) {
                        $ingredient = $ri->ingredient;
                        $rows[] = [
                            'ingredient_name' => $ingredient->name,
                            'amount' => (float) $ri->amount * $multiplier,
                            'unit' => $ingredient->unit,
                        ];
                    }
                }
            @endphp

            @if (count($rows) > 0)
                @foreach ($rows as $rIndex => $r)
                    <tr @if($gIndex > 0 && $rIndex === 0) class="menu-group-separator" @endif>
                        <td class="text-center">
                            @if ($rIndex === 0) {{ $group->date->format('Y-m-d') }} @endif
                        </td>
                        <td class="text-center">
                            @if ($rIndex === 0) {{ $group->name }} @endif
                        </td>
                        <td>
                            @if ($rIndex === 0) {{ $menuNames }} @endif
                        </td>
                        <td class="text-center">
                            @if ($rIndex === 0) {{ $groupPortions }} @endif
                        </td>
                        <td>{{ $r['ingredient_name'] }}</td>
                        <td class="text-right">{{ $fmt($r['amount']) }}</td>
                        <td class="text-center">{{ strtoupper($r['unit']) }}</td>
                    </tr>
                @endforeach
            @else
                <tr @if($gIndex > 0) class="menu-group-separator" @endif>
                    <td class="text-center">{{ $group->date->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $group->name }}</td>
                    <td>{{ $menuNames }}</td>
                    <td class="text-center">{{ $groupPortions }}</td>
                    <td colspan="3" class="text-center">Tidak ada bahan</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

<div>
    <p><strong>Total Menu Groups:</strong> {{ count($data) }}</p>
    <p><strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
</body>
</html>
