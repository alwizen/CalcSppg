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

                TextInput::make('province')
                    ->label('Provinsi')
                    ->placeholder('Jawa Tengah'),

                TextInput::make('regency')
                    ->label('Kabupaten / Kota')
                    ->placeholder('Kab. Tegal'),

                TextInput::make('district')
                    ->label('Kecamatan')
                    ->placeholder('Kramat'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('province')
                    ->label('Provinsi')
                    ->formatStateUsing(fn($state) => strtoupper($state)),
                TextColumn::make('regency')
                    ->label('Kabupaten')
                    ->formatStateUsing(fn($state) => strtoupper($state)),
                TextColumn::make('district')
                    ->label('Kecamatan')
                    ->formatStateUsing(fn($state) => strtoupper($state)),
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
