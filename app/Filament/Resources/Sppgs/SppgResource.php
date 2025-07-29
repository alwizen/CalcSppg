<?php

namespace App\Filament\Resources\Sppgs;

use App\Filament\Resources\Sppgs\Pages\ManageSppgs;
use App\Models\Sppg;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use UnitEnum;

class SppgResource extends Resource
{
    protected static ?string $model = Sppg::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'SPPG';

    protected static ?string $label = "Daftar SPPG";

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama SPPG')
                    ->required(),

                Select::make('province')
                    ->label('Provinsi')
                    ->options(function () {
                        $response = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
                        return collect($response->json())->pluck('name', 'name');
                    })
                    ->reactive()
                    ->afterStateUpdated(fn($set) => $set('regency', null)),

                Select::make('regency')
                    ->label('Kabupaten / Kota')
                    ->options(function (callable $get) {
                        $provinceId = $get('province');
                        if (!$provinceId) return [];

                        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
                        return collect($response->json())->pluck('name', 'name');
                    })
                    ->reactive()
                    ->afterStateUpdated(fn($set) => $set('district', null)),

                Select::make('district')
                    ->label('Kecamatan')
                    ->options(function (callable $get) {
                        $regencyId = $get('regency');
                        if (!$regencyId) return [];

                        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$regencyId}.json");
                        return collect($response->json())->pluck('name', 'name');
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('province')
                    ->label('Provinsi'),
                TextColumn::make('regency')
                    ->label('Kabupaten'),
                TextColumn::make('district')
                    ->label('Kecamatan'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSppgs::route('/'),
        ];
    }
}
