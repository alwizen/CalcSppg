<?php

namespace App\Filament\Resources\RecipeCalculations;

use App\Filament\Resources\RecipeCalculations\Pages\ManageRecipeCalculations;
use App\Models\Recipe;
use App\Models\RecipeCalculation;
use App\Models\RecipeCalculationIngredient;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipeCalculationResource extends Resource
{
    protected static ?string $model = RecipeCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Hitung Resep';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recipe_id')
                    ->label('Menu')
                    ->relationship('recipe', 'name')
                    ->required(),

                TextInput::make('requested_portions')
                    ->label('Jumlah Porsi')
                    ->numeric()
                    ->required(),
            ]);
    }

    // Observer akan handle creation logic

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('recipe.name')->label('Menu'),
                TextEntry::make('recipe.base_portions')->label('Porsi Dasar'),
                TextEntry::make('requested_portions')->label('Porsi Diminta'),

                // Debug info - hapus setelah berhasil
                TextEntry::make('debug_info')
                    ->label('Debug Info')
                    ->formatStateUsing(function ($record) {
                        $calculatedCount = $record->calculatedIngredients()->count();
                        $recipeIngredientsCount = $record->recipe->recipeIngredients()->count();
                        return "Calculated: {$calculatedCount}, Recipe Ingredients: {$recipeIngredientsCount}";
                    })
                    ->color('warning'),

                RepeatableEntry::make('calculatedIngredients')
                    ->label('Bahan yang Dibutuhkan')
                    ->contained(false)
                    ->schema([
                        TextEntry::make('ingredient.name')
                            ->hiddenLabel()
                            ->weight('medium'),
                        TextEntry::make('calculated_amount')
                            ->hiddenLabel()
                            ->numeric(decimalPlaces: 2),
                        TextEntry::make('unit')
                            ->hiddenLabel(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('recipe.name')
                    ->label('Menu')
                    ->searchable(),
                TextColumn::make('requested_portions')
                    ->label('Porsi Diminta')
                    ->suffix(' Porsi'),
                TextColumn::make('created_at')
                    ->label('Waktu Hitung')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('print_pdf')
                        ->label('Print PDF')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn(RecipeCalculation $record): string => route('recipe-calculation.pdf', $record))
                        ->openUrlInNewTab(),
                    Action::make('preview')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn(RecipeCalculation $record): string => route('recipe-calculation.preview', $record))
                        ->openUrlInNewTab(),
                ])->button()
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
            'index' => ManageRecipeCalculations::route('/'),
        ];
    }

    // Tambahkan method ini untuk memastikan relasi ter-load
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['recipe', 'calculatedIngredients.ingredient']);
    }
}
