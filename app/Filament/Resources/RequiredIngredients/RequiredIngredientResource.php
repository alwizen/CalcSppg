<?php

namespace App\Filament\Resources\RequiredIngredients;

use App\Filament\Resources\RequiredIngredients\Pages\ManageRequiredIngredients;
use App\Models\RequiredIngredient;
use App\Models\SupplierAllocation;
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
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RequiredIngredientResource extends Resource
{
    protected static ?string $model = RequiredIngredient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;


    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('menuGroup.name')
                    ->numeric(),
                TextEntry::make('ingredient.name')
                    ->numeric(),
                TextEntry::make('required_amount')
                    ->numeric(),
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
            ->columns([
                TextColumn::make('menuGroup.name')
                    ->label('Menu Group')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('menuGroup.date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Ingredient
                TextColumn::make('ingredient.name')
                    ->label('Bahan')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                // Kebutuhan (required_amount + unit)
                TextColumn::make('required_amount')
                    ->label('Kebutuhan')
                    ->alignRight()
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => number_format((float) $state, 3) . ' ' . $record->unit),

                // Teralokasi (sum allocations)
                TextColumn::make('allocated_sum')
                    ->label('Teralokasi')
                    ->alignRight()
                    ->getStateUsing(function ($record) {
                        $sum = SupplierAllocation::query()
                            ->where('menu_group_id', $record->menu_group_id)
                            ->where('ingredient_id', $record->ingredient_id)
                            ->sum('allocated_amount');

                        return number_format((float) $sum, 3) . ' ' . $record->unit;
                    }),

                // Progress (%)
                //    ProgressColumn::make('progress')
                //         ->label('Progress')
                //         ->getStateUsing(function ($record) {
                //             $allocated = (float) SupplierAllocation::query()
                //                 ->where('menu_group_id', $record->menu_group_id)
                //                 ->where('ingredient_id', $record->ingredient_id)
                //                 ->sum('allocated_amount');

                //             $required = (float) $record->required_amount;
                //             if ($required <= 0) return 0;
                //             $pct = ($allocated / $required) * 100;
                //             return (int) round(min(100, max(0, $pct)));
                //         })
                //         ->color(fn (int $state) => $state === 100 ? 'success' : 'warning'),

                // Status (Fully / Partial)
                BadgeColumn::make('status_alloc')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $allocated = (float) SupplierAllocation::query()
                            ->where('menu_group_id', $record->menu_group_id)
                            ->where('ingredient_id', $record->ingredient_id)
                            ->sum('allocated_amount');

                        return bccomp((string) $allocated, (string) $record->required_amount, 3) === 0
                            ? 'Full'
                            : 'Partial';
                    })
                    ->color(fn(string $state) => $state === 'Full' ? 'success' : 'warning'),
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
            'index' => ManageRequiredIngredients::route('/'),
        ];
    }
}
