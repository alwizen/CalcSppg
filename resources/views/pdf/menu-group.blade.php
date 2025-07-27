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

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Porsi</th>
                <th>Bahan-bahan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($menuGroup->recipes as $menuRecipe)
                @php
                    $recipe = $menuRecipe->recipe;
                    $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;
                    $ingredients = $recipe->recipeIngredients;
                    $rowspan = count($ingredients);
                @endphp

                @foreach ($ingredients as $index => $ri)
                    @php
                        $ingredient = $ri->ingredient;
                        $amount = $ri->amount * $multiplier;
                    @endphp
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $recipe->name }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $menuRecipe->requested_portions }}</td>
                        @endif
                        <td>{{ $ingredient->name }}</td>
                        <td>{{ rtrim(rtrim(number_format($amount, 4, '.', ''), '0'), '.') }}</td>
                        {{-- <td>{{ number_format($amount, 4) }}</td> --}}
                        <td>{{ $ingredient->unit }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
