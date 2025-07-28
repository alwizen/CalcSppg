<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kalkulasi Bahan - {{ $menuGroup->date->format('d-m-Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN KEBUTUHAN BAHAN DAPUR</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        <p><strong>Tanggal:</strong> {{ $menuGroup->date->format('d-m-Y') }}</p>
        <p><strong>Nama Perhitungan:</strong> {{ $menuGroup->name}}</p>
        <p><strong>Nama SPPG:</strong> {{ $menuGroup->sppg?->name ?? 'Belum dipilih' }}</p>
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
            @php
                // Gabungkan semua nama menu dengan koma
                $menuNames = $menuGroup->recipes->map(function($menuRecipe) {
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
                        $amount = $ri->amount * $multiplier; // Tidak dibulatkan, perhitungan real
                        
                        $allIngredients->push([
                            'name' => $ingredient->name,
                            'amount' => $amount,
                            'unit' => $ingredient->unit
                        ]);
                    }
                }
            @endphp

            @if($allIngredients->count() > 0)
                @foreach($allIngredients as $index => $ingredient)
                    <tr>
                        <td class="text-center">
                            @if($index === 0)
                                {{ $menuGroup->date->format('Y-m-d') }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($index === 0)
                                {{ $menuGroup->name }}
                            @endif
                        </td>
                        <td>
                            @if($index === 0)
                                {{ $menuNames }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($index === 0)
                                {{ $totalPortions }}
                            @endif
                        </td>
                        <td>{{ $ingredient['name'] }}</td>
                        <td class="text-center">{{ $ingredient['amount'] }}</td>
                        <td class="text-center">{{ $ingredient['unit'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center">{{ $menuGroup->date->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $menuGroup->name }}</td>
                    <td>{{ $menuNames }}</td>
                    <td class="text-center">{{ $totalPortions }}</td>
                    <td>Tidak ada bahan</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>