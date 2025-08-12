@php
    // Ambil group + relasi yang diperlukan
    $menuGroup = $record->load('recipes.recipe.recipeIngredients.ingredient');

    // Porsi global per menu group
    $groupPortions = (int) ($menuGroup->requested_portions ?? 1);
    if ($groupPortions < 1) $groupPortions = 1;

    $totalBahan = [];
@endphp

{{-- Tidak perlu CDN Tailwind di sini, Filament sudah bawa Tailwind --}}
<div style="border: 1px solid #d1d5db; border-radius: 8px; background-color: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow-x: auto;">
    {{-- TABEL DETAIL PER MENU --}}
    <div style="min-width: 120px; border-radius: 8px; border: 1px solid #9ca3af; margin-bottom: 24px;">
        <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f3f4f6; border-bottom: 1px solid #9ca3af;">
                    <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: left; font-weight: 600; color: #374151;">Nama Menu</th>
                    <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: center; font-weight: 600; color: #374151;">Porsi</th>
                    <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: left; font-weight: 600; color: #374151;">Bahan</th>
                    <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: right; font-weight: 600; color: #374151;">Jumlah</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Satuan</th>
                </tr>
            </thead>
            <tbody style="background-color: white;">
                @foreach ($menuGroup->recipes as $menuRecipe)
                    @php
                        $recipe = $menuRecipe->recipe;
                        $base = (float) ($recipe->base_portions ?? 1);
                        if ($base <= 0) $base = 1;

                        // multiplier berdasarkan porsi global group
                        $multiplier = $groupPortions / $base;
                        $firstRow = true;
                    @endphp

                    @foreach ($recipe->recipeIngredients as $ri)
                        @php
                            $ingredient = $ri->ingredient;
                            $jumlah = (float) $ri->amount * $multiplier;

                            // Rekap total bahan
                            $key = ($ingredient->name ?? '-') . '|' . ($ingredient->unit ?? '');
                            $totalBahan[$key] = ($totalBahan[$key] ?? 0) + $jumlah;
                        @endphp

                        <tr style="border-bottom: 1px solid #e5e7eb;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                            @if ($firstRow)
                                <td style="border-right: 1px solid #9ca3af; padding: 12px; font-weight: 500; color: #111827; background-color: #dbeafe;">
                                    {{ $recipe->name }}
                                </td>
                                <td style="border-right: 1px solid #9ca3af; padding: 12px; text-align: center; font-weight: 500; color: #2563eb; background-color: #dbeafe;">
                                    {{ $groupPortions }}
                                </td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="border-right: 1px solid #9ca3af; padding: 12px; background-color: #f9fafb;"></td>
                                <td style="border-right: 1px solid #9ca3af; padding: 12px; background-color: #f9fafb;"></td>
                            @endif

                            <td style="border-right: 1px solid #9ca3af; padding: 12px; color: #374151;">
                                {{ $ingredient->name }}
                            </td>
                            <td style="border-right: 1px solid #9ca3af; padding: 12px; text-align: right; font-family: monospace; color: #111827;">
                                {{ number_format($jumlah, 2, ',', '.') }}
                            </td>
                            <td style="padding: 12px; text-align: center; color: #6b7280; font-size: 12px; text-transform: uppercase;">
                                {{ $ingredient->unit }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- OPSIONAL: TABEL REKAP TOTAL BAHAN --}}
    @if(!empty($totalBahan))
        <div style="min-width: 120px; border-radius: 8px; border: 1px solid #9ca3af;">
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f3f4f6; border-bottom: 1px solid #9ca3af;">
                        <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: left; font-weight: 600; color: #374151;">Bahan</th>
                        <th style="border-right: 1px solid #9ca3af; padding: 12px; text-align: right; font-weight: 600; color: #374151;">Total</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (collect($totalBahan)->sortKeys() as $key => $jumlah)
                        @php [$nama, $unit] = explode('|', $key); @endphp
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="border-right: 1px solid #9ca3af; padding: 12px;">{{ $nama }}</td>
                            <td style="border-right: 1px solid #9ca3af; padding: 12px; text-align:right; font-family: monospace;">
                                {{ number_format($jumlah, 2, ',', '.') }}
                            </td>
                            <td style="padding: 12px; text-align: center; text-transform: uppercase; color: #6b7280;">
                                {{ $unit }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
