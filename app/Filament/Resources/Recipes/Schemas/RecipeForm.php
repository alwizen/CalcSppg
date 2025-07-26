<?php

namespace App\Filament\Resources\Recipes\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info Menu')
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->columns(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Resep')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('base_portions')
                                    ->label('Porsi Dasar')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                Toggle::make('is_active')
                                    ->helperText('Tampilkan di menu')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),

                        Section::make('Bahan-bahan')
                            ->schema([
                                Repeater::make('ingredients')
                                    // ->label('Bahan-bahan')
                                    ->relationship()
                                    ->schema([
                                        Select::make('ingredient_id')
                                            ->relationship('ingredients', 'name'),
                                        TextInput::make('amount')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->step(0.1)
                                            ->required(),
                                        Select::make('unit')
                                            ->label('Satuan')
                                            ->options([
                                                'Kg' => 'Kg',
                                                'Liter' => 'Liter',
                                                'Botol' => 'Botol',
                                                'Pouch' => 'Pouch',
                                                'Batang' => 'Batang',
                                                'Ons' => 'Ons',
                                                'Ikat' => 'Ikat'
                                            ])
                                            ->required(),
                                    ])->columns(3),
                            ])
                    ])

            ]);
    }
}
