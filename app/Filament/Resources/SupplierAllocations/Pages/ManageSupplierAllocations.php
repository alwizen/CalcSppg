<?php

namespace App\Filament\Resources\SupplierAllocations\Pages;

use App\Filament\Resources\SupplierAllocations\SupplierAllocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSupplierAllocations extends ManageRecords
{
    protected static string $resource = SupplierAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
