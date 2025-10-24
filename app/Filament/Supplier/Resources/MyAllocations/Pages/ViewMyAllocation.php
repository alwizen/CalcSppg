<?php

namespace App\Filament\Supplier\Resources\MyAllocations\Pages;

use App\Filament\Supplier\Resources\MyAllocations\MyAllocationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMyAllocation extends ViewRecord
{
    protected static string $resource = MyAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
