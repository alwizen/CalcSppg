<?php

namespace App\Filament\Supplier\Resources\MyAllocations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyAllocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(function (Builder $q) {
            //     $panel = Filament::getCurrentPanel();
            //     if ($panel && $panel->getId() === 'supplier') {
            //         $guard = $panel->getAuthGuard() ?? config('auth.defaults.guard');
            //         $supplierId = auth($guard)->id();
            //         $q->where('supplier_id', $supplierId);
            //     }
            // })
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
