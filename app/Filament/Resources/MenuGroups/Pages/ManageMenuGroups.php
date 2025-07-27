<?php

namespace App\Filament\Resources\MenuGroups\Pages;

use App\Filament\Resources\MenuGroups\MenuGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMenuGroups extends ManageRecords
{
    protected static string $resource = MenuGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
