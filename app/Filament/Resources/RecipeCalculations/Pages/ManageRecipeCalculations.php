<?php

namespace App\Filament\Resources\RecipeCalculations\Pages;

use App\Filament\Resources\RecipeCalculations\RecipeCalculationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManageRecipeCalculations extends ManageRecords
{
    protected static string $resource = RecipeCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with(['recipe', 'calculatedIngredients.ingredient']);
    }

    protected function resolveRecord($key): Model
    {
        return $this->getModel()::with(['recipe', 'calculatedIngredients.ingredient'])
            ->findOrFail($key);
    }
}
