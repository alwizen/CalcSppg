<?php

namespace App\Filament\Resources\Sppgs\Pages;

use App\Filament\Resources\Sppgs\SppgResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSppgs extends ManageRecords
{
    protected static string $resource = SppgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah SPPG'),
        ];
    }
}
