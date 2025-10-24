<?php

namespace App\Filament\Resources\RequiredIngredients\Pages;

use App\Filament\Resources\RequiredIngredients\RequiredIngredientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRequiredIngredients extends ManageRecords
{
    protected static string $resource = RequiredIngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
