<?php

namespace App\Filament\Supplier\Resources\MyAllocations;

use App\Filament\Supplier\Resources\MyAllocations\Pages\CreateMyAllocation;
use App\Filament\Supplier\Resources\MyAllocations\Pages\EditMyAllocation;
use App\Filament\Supplier\Resources\MyAllocations\Pages\ListMyAllocations;
use App\Filament\Supplier\Resources\MyAllocations\Pages\ViewMyAllocation;
use App\Filament\Supplier\Resources\MyAllocations\Schemas\MyAllocationForm;
use App\Filament\Supplier\Resources\MyAllocations\Schemas\MyAllocationInfolist;
use App\Filament\Supplier\Resources\MyAllocations\Tables\MyAllocationsTable;
use App\Models\MyAllocation;
use App\Models\SupplierAllocation;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
            ->where('supplier_id', auth($guard)->id());  // <- ambil ID supplier yg login
    }

    public static function form(Schema $schema): Schema
    {
        return MyAllocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyAllocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyAllocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyAllocations::route('/'),
            'create' => CreateMyAllocation::route('/create'),
            'view' => ViewMyAllocation::route('/{record}'),
            'edit' => EditMyAllocation::route('/{record}/edit'),
        ];
    }
}
