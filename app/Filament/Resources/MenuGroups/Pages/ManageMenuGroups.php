<?php

namespace App\Filament\Resources\MenuGroups\Pages;

use App\Filament\Resources\MenuGroups\MenuGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageMenuGroups extends ManageRecords
{
    protected static string $resource = MenuGroupResource::class;

    protected function beforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Hitung Kebutuhan')
                ->icon(Heroicon::OutlinedCalculator),
        ];
    }
}
