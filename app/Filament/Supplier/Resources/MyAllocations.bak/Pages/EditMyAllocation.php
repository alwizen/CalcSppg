<?php

namespace App\Filament\Supplier\Resources\MyAllocations\Pages;

use App\Filament\Supplier\Resources\MyAllocations\MyAllocationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMyAllocation extends EditRecord
{
    protected static string $resource = MyAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
