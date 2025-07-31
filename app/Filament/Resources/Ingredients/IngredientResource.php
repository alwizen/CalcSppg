<?php

namespace App\Filament\Resources\Ingredients;

use App\Filament\Resources\Ingredients\Pages\ManageIngredients;
use App\Models\Ingredient;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    protected static ?string $navigationLabel = "Bahan Baku";

    protected static ?string $label = "Bahan Baku";

    protected static string|UnitEnum|null $navigationGroup = "Master Data";

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars3BottomLeft;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nama Bahan Baku'),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'buah' => 'Buah',
                        'bumbu' => 'Bumbu',
                        'daging' => 'Daging',
                        'sereal' => 'Sereal',
                        'sayuran' => 'Sayuran',
                        'protein' => 'Protein',
                        'olahan' => 'Olahan',
                        'minyak' => 'minyak'
                    ])
                    ->required()
                    ->placeholder('Masukkan kategori bahan baku'),
                Select::make('unit')
                    ->label('Satuan')
                    ->required()
                    ->options(
                        [
                            'kg' => 'Kg',
                            'liter' => 'Liter',
                            'pack' => 'Pack',
                            'pouch' => 'Pouch',
                            'batang' => 'batang',
                            'liter' => 'Liter',
                            'pcs' => 'Pcs'
                        ]
                    )
                    ->placeholder('Masukkan satuan bahan baku'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('category'),
                TextEntry::make('unit'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->label('Nama Bahan'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Satuan')
                    ->searchable(),
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
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(function () {
                        $categories = Ingredient::query()->distinct()->pluck('category', 'category')->filter();
                        return $categories->isNotEmpty() ? $categories->toArray() : [];
                    })
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ManageIngredients::route('/'),
        ];
    }
}
