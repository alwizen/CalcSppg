<?php

namespace App\Filament\Resources\MenuGroups;

use App\Filament\Resources\MenuGroups\Pages\ManageMenuGroups;
use App\Models\MenuGroup;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuGroupResource extends Resource
{
    protected static ?string $model = MenuGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('name')
                    ->label('Nama Kalkulasi')
                    ->required(),

                Repeater::make('recipes')
                    ->relationship()
                    ->schema([
                        Select::make('recipe_id')
                            ->label('Resep')
                            ->relationship('recipe', 'name')
                            ->required(),

                        TextInput::make('requested_portions')
                            ->label('Jumlah Porsi')
                            ->numeric()
                            ->required(),
                    ])
                    ->label('Daftar Resep')
                    ->columns(2)
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('kalkulasi_bahan')
                    ->label('Kalkulasi Bahan')
                    ->view('infolists.menu-group')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('recipes_portions')
                    ->label('Menu')
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return $record->recipes->map(function ($menuRecipe) {
                            return "{$menuRecipe->recipe->name}";
                        })->toArray();
                    }),

                TextColumn::make('portions')
                    ->label('Porsi')
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return $record->recipes->map(function ($menuRecipe) {
                            return "{$menuRecipe->requested_portions} porsi";
                        })->toArray();
                    }),

                // TextColumn::make('ingredients_summary')
                //     ->label('Bahan')
                //     ->listWithLineBreaks()
                //     ->getStateUsing(function ($record) {
                //         return $record->recipes->flatMap(function ($menuRecipe) {
                //             $recipe = $menuRecipe->recipe;
                //             $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;

                //             return $recipe->recipeIngredients->map(function ($ri) use ($multiplier) {
                //                 $ingredient = $ri->ingredient;
                //                 $amount = round($ri->amount * $multiplier, 2);
                //                 return "{$ingredient->name}: {$amount} {$ingredient->unit}";
                //             });
                //         })->toArray();
                //     }),



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
            'index' => ManageMenuGroups::route('/'),
        ];
    }
}
