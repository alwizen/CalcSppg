<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kalkulasi Bahan - {{ $menuGroup->date->format('d-m-Y') }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <h2>Kalkulasi Kebutuhan Bahan</h2>
    <p><strong>Tanggal:</strong> {{ $menuGroup->date->format('d-m-Y') }}</p>
    <p><strong>Nama Perhitungan:</strong> {{ $menuGroup->name}}</p>
    <p><strong>Nama SPPG:</strong> {{ $menuGroup->sppg?->name ?? 'Belum dipilih' }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Porsi</th>
                <th>Bahan-bahan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Collect all ingredients grouped by ingredient name
                $ingredientGroups = [];
                $menuNames = [];
                
                foreach ($menuGroup->recipes as $menuRecipe) {
                    $recipe = $menuRecipe->recipe;
                    $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;
                    $menuNames[] = $recipe->name;
                    
                    foreach ($recipe->recipeIngredients as $ri) {
                        $ingredient = $ri->ingredient;
                        $amount = $ri->amount * $multiplier;
                        $key = $ingredient->name;
                        
                        if (!isset($ingredientGroups[$key])) {
                            $ingredientGroups[$key] = [
                                'name' => $ingredient->name,
                                'unit' => $ingredient->unit,
                                'total_amount' => 0
                            ];
                        }
                        
                        $ingredientGroups[$key]['total_amount'] += $amount;
                    }
                }
                
                $menuNamesString = implode(', ', $menuNames);
                $totalPortions = $menuGroup->recipes->sum('requested_portions');
                $rowspan = count($ingredientGroups);
            @endphp

            @foreach ($ingredientGroups as $index => $ingredientData)
                <tr>
                    @if ($index === 0 || $loop->first)
                        <td rowspan="{{ $rowspan }}">{{ $menuNamesString }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $totalPortions }}</td>
                    @endif
                    <td>{{ $ingredientData['name'] }}</td>
                    <td>{{ rtrim(rtrim(number_format($ingredientData['total_amount'], 4, '.', ''), '0'), '.') }}</td>
                    <td>{{ $ingredientData['unit'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>