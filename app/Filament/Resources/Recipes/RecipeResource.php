<?php

namespace App\Filament\Resources\Recipes;

use App\Filament\Resources\Recipes\Pages\ManageRecipes;
use App\Models\Recipe;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use UnitEnum;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = "Master Data";

    protected static ?int $navigationSort = 0;

    protected static ?string $label = "Daftar Menu";

    protected static ?string $navigationLabel = "Menu Masakan";

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Menu Masakan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('base_portions')
                    ->label('Porsi Dasar')
                    // ->readOnly()
                    ->numeric()
                    ->default(1000)
                    ->required(),

                Toggle::make('is_active')
                    ->helperText('Tampilkan di menu')
                    ->label('Aktif')
                    ->default(true),


                Repeater::make('recipeIngredients')
                    ->table([
                        TableColumn::make('Bahan Baku'),
                        TableColumn::make('Jumlah')
                    ])
                    ->label('Bahan Baku')
                    ->relationship('recipeIngredients')
                    ->schema([
                        Select::make('ingredient_id')
                            ->label('Bahan Baku')
                            ->relationship('ingredient', 'name')
                            ->required(),

                        TextInput::make('amount')
                            ->label('Jumlah')
                            ->numeric()
                            ->step(0.00001)
                            ->required()
                            ->placeholder('Jumlah Bahan yang dibutuhkan'),

                    ])
                    ->columnSpanFull()
                    ->columns(2)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable()
                    ->formatStateUsing(fn($state) => strtoupper($state)),
                TextColumn::make('base_portions')
                    ->numeric()
                    ->suffix(' Porsi')
                    ->label('Acuan Porsi'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Tampilkan'),
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
                // ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRecipes::route('/'),
        ];
    }
}
