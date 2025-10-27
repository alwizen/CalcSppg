<?php

namespace App\Filament\Supplier\Resources\MyAllocations\Pages;

use App\Filament\Supplier\Resources\MyAllocations\MyAllocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMyAllocations extends ManageRecords
{
    protected static string $resource = MyAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
