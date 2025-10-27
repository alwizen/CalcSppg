<?php

namespace App\Filament\Resources\SupplierAllocations;

use App\Filament\Resources\SupplierAllocations\Pages\ManageSupplierAllocations;
use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\SupplierAllocation;
use BackedEnum;
use Closure;
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
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

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
                        if (!$ingredientId) return [];

                        return Supplier::whereHas('ingredients', fn($q) => $q->where('ingredient_id', $ingredientId))
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->helperText('Only suppliers who provide this ingredient')

                    // ⬇️ Cegah duplikasi supplier untuk kombinasi (menu_group_id, ingredient_id)
                    ->unique(
                        ignoreRecord: true,                 // saat edit: abaikan dirinya sendiri
                        column: 'supplier_id',
                        modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                            // ❗ Jangan panggil ->table(), cukup tambahkan where-where pembatas
                            return $rule
                                ->where('menu_group_id', $get('menu_group_id'))
                                ->where('ingredient_id',  $get('ingredient_id'));
                        },
                    ),
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
                    // HelperText: tampilkan Needed / Allocated / Remaining (live)
                    ->helperText(function ($get) {
                        $menuGroupId  = $get('menu_group_id');
                        $ingredientId = $get('ingredient_id');

                        if (!$menuGroupId || !$ingredientId) {
                            return 'Select menu group and ingredient first';
                        }

                        $menuGroup  = \App\Models\MenuGroup::with('recipes.recipe.recipeIngredients')->find($menuGroupId);
                        $ingredient = \App\Models\Ingredient::find($ingredientId);
                        if (!$menuGroup || !$ingredient) {
                            return '';
                        }

                        // Hitung total kebutuhan
                        $requestedPortions = $menuGroup->requested_portions ?? 1;
                        $totalNeeded = 0.0;

                        foreach ($menuGroup->recipes as $mgr) {
                            $recipe = $mgr->recipe;
                            $base   = $recipe->base_portions ?? 1;

                            $ri = $recipe->recipeIngredients()
                                ->where('ingredient_id', $ingredientId)
                                ->first();

                            if ($ri) {
                                $totalNeeded += ($ri->amount / max($base, 1)) * $requestedPortions;
                            }
                        }

                        // Hitung alokasi yang sudah ada (exclude record saat ini jika edit)
                        $currentId = $get('../../record.id') ?? $get('record.id');
                        $allocated = SupplierAllocation::query()
                            ->where('menu_group_id', $menuGroupId)
                            ->where('ingredient_id', $ingredientId)
                            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                            ->sum('quantity');

                        $remaining = max($totalNeeded - $allocated, 0);
                        $unit = $ingredient->unit ?? '';

                        if ($totalNeeded <= 0) {
                            return "⚠️ This ingredient is not used in selected menu group";
                        }

                        return "📊 Needed: " . number_format($totalNeeded, 3) . " {$unit} · "
                            . "Allocated: " . number_format($allocated, 3) . " {$unit} · "
                            . "Remaining: " . number_format($remaining, 3) . " {$unit}";
                    })
                    // Rule: quantity tidak boleh melebihi sisa kebutuhan
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                $menuGroupId  = $get('menu_group_id');
                                $ingredientId = $get('ingredient_id');

                                if (!$menuGroupId || !$ingredientId) {
                                    return;
                                }

                                $menuGroup = \App\Models\MenuGroup::with('recipes.recipe.recipeIngredients')->find($menuGroupId);
                                if (!$menuGroup) {
                                    return;
                                }

                                // Total needed
                                $requestedPortions = $menuGroup->requested_portions ?? 1;
                                $totalNeeded = 0.0;

                                foreach ($menuGroup->recipes as $mgr) {
                                    $recipe = $mgr->recipe;
                                    $base   = $recipe->base_portions ?? 1;

                                    $ri = $recipe->recipeIngredients()
                                        ->where('ingredient_id', $ingredientId)
                                        ->first();

                                    if ($ri) {
                                        $totalNeeded += ($ri->amount / max($base, 1)) * $requestedPortions;
                                    }
                                }

                                // Sudah dialokasikan (exclude current record saat edit)
                                $currentId = $get('../../record.id') ?? $get('record.id');
                                $allocated = SupplierAllocation::query()
                                    ->where('menu_group_id', $menuGroupId)
                                    ->where('ingredient_id', $ingredientId)
                                    ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                                    ->sum('quantity');

                                $remaining = $totalNeeded - $allocated;

                                $val = (float) $value;
                                if ($val < 0) {
                                    $fail('Quantity must be >= 0.');
                                    return;
                                }

                                if ($remaining < 0) {
                                    $fail('Total allocation already exceeds the needed amount. Please adjust existing allocations.');
                                    return;
                                }
                            };
                        },
                    ])
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

                TextColumn::make('menuGroup.allocations.date')
                    ->label('tanggal'),

                TextColumn::make('quantity')
                    ->formatStateUsing(fn($record) => $record->quantity . ' ' . $record->unit),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menuGroup.date')
                    ->date()
                    ->label('Tanggal'),

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

                TextColumn::make('menuGroup.sppg.name')
                    ->label('SPPG')
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
