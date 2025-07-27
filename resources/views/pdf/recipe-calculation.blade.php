<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Perhitungan Resep - {{ $recipe->name }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
                background: #fff;
            }

            .container {
                max-width: 100%;
                margin: 0 auto;
                padding: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #2563eb;
                padding-bottom: 15px;
            }

            .header h1 {
                font-size: 24px;
                color: #1e40af;
                margin-bottom: 5px;
            }

            .header p {
                font-size: 14px;
                color: #6b7280;
            }

            .recipe-info {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 25px;
            }

            .recipe-info h2 {
                font-size: 18px;
                color: #1e40af;
                margin-bottom: 15px;
                border-bottom: 1px solid #cbd5e1;
                padding-bottom: 8px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }

            .info-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
            }

            .info-label {
                font-weight: 600;
                color: #374151;
            }

            .info-value {
                color: #1f2937;
                font-weight: 500;
            }

            .ingredients-section {
                margin-bottom: 25px;
            }

            .ingredients-title {
                font-size: 18px;
                color: #1e40af;
                margin-bottom: 15px;
                border-bottom: 1px solid #cbd5e1;
                padding-bottom: 8px;
            }

            .ingredients-table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                overflow: hidden;
            }

            .ingredients-table th {
                background: #1e40af;
                color: white;
                padding: 12px 15px;
                text-align: left;
                font-weight: 600;
                font-size: 13px;
            }

            .ingredients-table td {
                padding: 12px 15px;
                border-bottom: 1px solid #e2e8f0;
            }

            .ingredients-table tr:nth-child(even) {
                background: #f8fafc;
            }

            .ingredients-table tr:last-child td {
                border-bottom: none;
            }

            .ingredient-name {
                font-weight: 500;
                color: #1f2937;
            }

            .ingredient-amount {
                text-align: right;
                font-weight: 600;
                color: #059669;
            }

            .ingredient-unit {
                color: #6b7280;
                font-size: 11px;
            }

            .footer {
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #e2e8f0;
                text-align: center;
                color: #6b7280;
                font-size: 11px;
            }

            .calculation-summary {
                background: #ecfdf5;
                border: 1px solid #a7f3d0;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .calculation-summary h3 {
                color: #065f46;
                font-size: 14px;
                margin-bottom: 8px;
            }

            .calculation-formula {
                font-family: "Courier New", monospace;
                background: #fff;
                padding: 8px 12px;
                border-radius: 4px;
                border: 1px solid #d1fae5;
                color: #047857;
                font-size: 11px;
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>Perhitungan Resep Masakan</h1>
                <p>
                    Daftar bahan yang dibutuhkan berdasarkan porsi yang diminta
                </p>
            </div>

            <!-- Recipe Info -->
            <div class="recipe-info">
                <h2>Informasi Resep</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Menu:</span>
                        <span class="info-value">{{ $recipe->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Porsi Dasar:</span>
                        <span class="info-value"
                            >{{ $recipe->base_portions }} porsi</span
                        >
                    </div>
                    <div class="info-item">
                        <span class="info-label">Porsi Diminta:</span>
                        <span class="info-value"
                            >{{ $calculation->requested_portions }} porsi</span
                        >
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Hitung:</span>
                        <span
                            class="info-value"
                            >{{ $calculation->created_at->format('d/m/Y H:i') }}</span
                        >
                    </div>
                </div>
            </div>

            <!-- Calculation Summary -->
            <div class="calculation-summary">
                <h3>Rumus Perhitungan</h3>
                <div class="calculation-formula">
                    Jumlah Bahan = (Porsi Diminta ÷ Porsi Dasar) × Jumlah Bahan
                    Dasar<br />
                    Multiplier = {{ $calculation->requested_portions }} ÷
                    {{ $recipe->base_portions }} =
                    {{ round($calculation->requested_portions / $recipe->base_portions, 2) }}
                </div>
            </div>

            <!-- Ingredients List -->
            <div class="ingredients-section">
                <h2 class="ingredients-title">Daftar Bahan yang Dibutuhkan</h2>

                @if($ingredients->count() > 0)
                <table class="ingredients-table">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 50%">Nama Bahan</th>
                            <th style="width: 25%">Jumlah</th>
                            <th style="width: 20%">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredients as $index => $ingredient)
                        <tr>
                            <td style="text-align: center">{{ $index + 1 }}</td>
                            <td class="ingredient-name">
                                {{ $ingredient->ingredient->name }}
                            </td>
                            <td class="ingredient-amount">
                                {{ number_format($ingredient->calculated_amount, 2) }}
                            </td>
                            <td class="ingredient-unit">
                                {{ $ingredient->unit }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div style="text-align: center; padding: 40px; color: #6b7280">
                    <p>Tidak ada bahan yang ditemukan untuk resep ini.</p>
                </div>
                @endif
            </div>

            @if($recipe->description)
            <div class="recipe-info">
                <h2>Deskripsi Resep</h2>
                <p style="color: #4b5563; line-height: 1.6">
                    {{ $recipe->description }}
                </p>
            </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <p>
                    Dokumen ini dibuat secara otomatis pada {{ $generated_at }}
                </p>
                <p>Sistem Perhitungan Resep Masakan</p>
            </div>
        </div>
    </body>
</html>
