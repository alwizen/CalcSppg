<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kalkulasi Bahan - {{ $menuGroup->date->format('d-m-Y') }}</title>
    <style>
        /* aman untuk dompdf */
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; margin: 20px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0 0 6px; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 12px; }
        .info { margin-bottom: 10px; }
        .info p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        th { background: #4472C4; color: #fff; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .muted { color: #555; }
        .section { margin-top: 24px; }
    </style>
</head>
<body>
@php
    // pastikan relasi lengkap
    $menuGroup = $menuGroup->load('recipes.recipe.recipeIngredients.ingredient', 'sppg');

    // porsi global (fallback 1)
    $groupPortions = (int) ($menuGroup->requested_portions ?? 1);
    if ($groupPortions < 1) $groupPortions = 1;

    // gabungkan nama menu
    $menuNames = $menuGroup->recipes->map(fn($mr) => $mr->recipe->name)->join(', ');

    // siapkan list detail & rekap
    $rows = [];
    $rekap = [];
    foreach ($menuGroup->recipes as $menuRecipe) {
        $recipe = $menuRecipe->recipe;
        $base = (float) ($recipe->base_portions ?? 1);
        if ($base <= 0) $base = 1;
        $multiplier = $groupPortions / $base;

        foreach ($recipe->recipeIngredients as $ri) {
            $ingredient = $ri->ingredient;
            $amount = (float) $ri->amount * $multiplier;

            $rows[] = [
                'recipe_name' => $recipe->name,
                'ingredient_name' => $ingredient->name,
                'amount' => $amount,
                'unit' => $ingredient->unit,
            ];

            $key = ($ingredient->name ?? '-') . '|' . ($ingredient->unit ?? '');
            $rekap[$key] = ($rekap[$key] ?? 0) + $amount;
        }
    }

    // format helper
    $fmt = fn($n) => number_format((float)$n, 2, ',', '.');
@endphp

<div class="header">
    <h1>LAPORAN KEBUTUHAN BAHAN DAPUR</h1>
    <p class="muted">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>

<div class="info">
    <p><strong>Tanggal:</strong> {{ $menuGroup->date->format('d-m-Y') }}</p>
    <p><strong>Hari Ke-:</strong> {{ $menuGroup->name }}</p>
    <p><strong>Nama SPPG:</strong> {{ $menuGroup->sppg?->name ?? 'Belum dipilih' }}</p>
    <p><strong>Jumlah Porsi:</strong> {{ $groupPortions }}</p>
</div>

{{-- Tabel detail per menu (dirangkum dalam satu blok) --}}
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
        @if (count($rows) > 0)
            @foreach ($rows as $idx => $r)
                <tr>
                    <td class="text-center">
                        @if ($idx === 0) {{ $menuGroup->date->format('Y-m-d') }} @endif
                    </td>
                    <td class="text-center">
                        @if ($idx === 0) {{ $menuGroup->name }} @endif
                    </td>
                    <td>
                        @if ($idx === 0) {{ $menuNames }} @endif
                    </td>
                    <td class="text-center">
                        @if ($idx === 0) {{ $groupPortions }} @endif
                    </td>
                    <td>{{ $r['ingredient_name'] }}</td>
                    <td class="text-right">{{ $fmt($r['amount']) }}</td>
                    <td class="text-center">{{ strtoupper($r['unit']) }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center">{{ $menuGroup->date->format('Y-m-d') }}</td>
                <td class="text-center">{{ $menuGroup->name }}</td>
                <td>{{ $menuNames }}</td>
                <td class="text-center">{{ $groupPortions }}</td>
                <td colspan="3" class="text-center muted">Tidak ada bahan</td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Rekap total bahan --}}
@if (!empty($rekap))
    <div class="section">
        <h3 style="margin:0 0 6px;">Rekap Total Bahan</h3>
        <table>
            <thead>
                <tr>
                    <th width="50%">Bahan</th>
                    <th width="25%">Total</th>
                    <th width="25%">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($rekap)->sortKeys() as $key => $total)
                    @php [$nama, $unit] = explode('|', $key); @endphp
                    <tr>
                        <td>{{ $nama }}</td>
                        <td class="text-right">{{ $fmt($total) }}</td>
                        <td class="text-center">{{ strtoupper($unit) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</body>
</html>
