<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Menu Groups</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
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
        .page-break {
            page-break-before: always;
        }
        .menu-group-separator {
            border-top: 2px solid #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEBUTUHAN BAHAN DAPUR</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i:s') }}</p>
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
            @foreach($data as $menuData)
                @if($menuData['ingredients']->count() > 0)
                    @foreach($menuData['ingredients'] as $index => $ingredient)
                        <tr>
                            <td class="text-center">
                                @if($index === 0)
                                    {{ $menuData['menu_group']->date->format('Y-m-d') }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($index === 0)
                                    {{ $menuData['menu_group']->name }}
                                @endif
                            </td>
                            <td>
                                @if($index === 0)
                                    {{ $menuData['menu_names'] }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if($index === 0)
                                    {{ $menuData['total_portions'] }}
                                @endif
                            </td>
                            <td>{{ $ingredient['name'] }}</td>
                            <td class="text-center">{{ $ingredient['amount'] }}</td>
                            <td class="text-center">{{ $ingredient['unit'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $menuData['menu_group']->date->format('Y-m-d') }}</td>
                        <td class="text-center">{{ $menuData['menu_group']->name }}</td>
                        <td>{{ $menuData['menu_names'] }}</td>
                        <td class="text-center">{{ $menuData['total_portions'] }}</td>
                        <td>Tidak ada bahan</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Total Menu Groups:</strong> {{ count($data) }}</p>
        <p><strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>