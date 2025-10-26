<?php

namespace App\Filament\Resources\SupplierAllocations;

use App\Filament\Resources\SupplierAllocations\Pages\ManageSupplierAllocations;
use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\SupplierAllocation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierAllocationResource extends Resource
{
    protected static ?string $model = SupplierAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('menu_group_id')
                    ->label('Menu Group')
                    ->relationship('menuGroup', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('ingredient_id', null)),


                Select::make('ingredient_id')
                    ->label('Ingredient')
                    ->options(function (callable $get) {
                        $menuGroupId = $get('menu_group_id');
                        if (!$menuGroupId) {
                            return [];
                        }

                        return Ingredient::whereHas('recipes.menuGroupRecipes', function ($query) use ($menuGroupId) {
                            $query->where('menu_group_id', $menuGroupId);
                        })->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('supplier_id', null))
                    ->searchable(),

                Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(function (callable $get) {
                        $ingredientId = $get('ingredient_id');
                        if (!$ingredientId) {
                            return [];
                        }

                        return Supplier::whereHas('ingredients', function ($query) use ($ingredientId) {
                            $query->where('ingredient_id', $ingredientId);
                        })->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->helperText('Only suppliers who provide this ingredient'),

                TextInput::make('quantity')
                    ->label('Quantity to Allocate')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix(function (callable $get) {
                        $ingredientId = $get('ingredient_id');
                        if (!$ingredientId) {
                            return '';
                        }
                        $ingredient = \App\Models\Ingredient::find($ingredientId);
                        return $ingredient?->unit ?? '';
                    })
                    ->helperText(function (callable $get) {
                        $menuGroupId = $get('menu_group_id');
                        $ingredientId = $get('ingredient_id');

                        if (!$menuGroupId || !$ingredientId) {
                            return 'Select menu group and ingredient first';
                        }

                        // Hitung total kebutuhan ingredient dari semua recipes di menu group
                        $menuGroup = \App\Models\MenuGroup::find($menuGroupId);
                        $ingredient = \App\Models\Ingredient::find($ingredientId);

                        if (!$menuGroup || !$ingredient) {
                            return '';
                        }

                        $totalNeeded = 0;
                        $requestedPortions = $menuGroup->requested_portions ?? 1;

                        // Loop semua recipes di menu group
                        foreach ($menuGroup->recipes as $menuGroupRecipe) {
                            $recipe = $menuGroupRecipe->recipe;

                            // Cari ingredient di recipe
                            $recipeIngredient = $recipe->recipeIngredients()
                                ->where('ingredient_id', $ingredientId)
                                ->first();

                            if ($recipeIngredient) {
                                // Hitung berdasarkan porsi yang diminta
                                $basePortions = $recipe->base_portions ?? 1;
                                $amount = $recipeIngredient->amount;
                                $neededAmount = ($amount / $basePortions) * $requestedPortions;
                                $totalNeeded += $neededAmount;
                            }
                        }

                        if ($totalNeeded > 0) {
                            return "📊 Total needed: " . number_format($totalNeeded, 2) . " " . ($ingredient->unit ?? '');
                        }

                        return "⚠️ This ingredient is not used in selected menu group";
                    })
                    ->reactive(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextColumn::make('menuGroup.name')
                    ->label('Menu Group')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ingredient.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->formatStateUsing(fn($record) => $record->quantity . ' ' . $record->unit),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menuGroup.name')
                    ->label('Menu Group')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ingredient.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->formatStateUsing(fn($record) => $record->quantity . ' ' . $record->unit),

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
            'index' => Pages\ManageSupplierAllocations::route('/'),
            // 'bulk-allocate' => Pages\BulkAllocateSupplierAllocations::route('/bulk-allocate'),
        ];
    }
}
