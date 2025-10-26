<?php

namespace App\Filament\Supplier\Resources\MyAllocations;

use App\Filament\Supplier\Resources\MyAllocations\Pages\ManageMyAllocations;
use App\Models\SupplierAllocation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyAllocationResource extends Resource
{
    protected static ?string $model = SupplierAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $guard = Filament::getCurrentPanel()?->getAuthGuard() ?? 'suppliers';

        return parent::getEloquentQuery()
            ->where('supplier_id', auth($guard)->id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
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
            'index' => ManageMyAllocations::route('/'),
        ];
    }
}
