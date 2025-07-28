<?php

namespace App\Filament\Resources\MenuGroups;

use App\Filament\Resources\MenuGroups\Pages\ManageMenuGroups;
use App\Models\MenuGroup;
use BackedEnum;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MenuGroupResource extends Resource
{
    protected static ?string $model = MenuGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = "Hitung Kebutuhan";

    protected static ?string $label = "Hitung Kebutuhan Dapur";

    public static function getNavigationBadge(): ?string
    {
        return "🧮";
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Nama Kalkulasi')
                    ->required()
                    ->default(now()),

                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required()
                    ->placeholder('Menu Hari ke'),

                Select::make('sppg_id')
                    ->relationship('sppg', 'name')
                    ->label('Nama SPPG'),

                Repeater::make('recipes')
                    ->relationship()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('recipe_id')
                            ->label('Menu Masakan')
                            ->relationship('recipe', 'name')
                            ->required(),

                        TextInput::make('requested_portions')
                            ->label('Jumlah Porsi')
                            ->numeric()
                            ->required(),
                    ])
                    ->label('Daftar Resep')
                    ->columns(2)
                    ->required(),
            ])->columns(3);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewEntry::make('kalkulasi_bahan')
                    ->label('Kalkulasi Bahan')
                    ->view('infolists.menu-group')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->searchable()
                    ->label('Tanggal')
                    ->date(),
                TextColumn::make('recipes_portions')
                    ->label('Nama Menu')
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return $record->recipes->map(function ($menuRecipe) {
                            return "{$menuRecipe->recipe->name}";
                        })->toArray();
                    }),

                TextColumn::make('portions')
                    ->label('Porsi')
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return $record->recipes->map(function ($menuRecipe) {
                            return "{$menuRecipe->requested_portions} porsi";
                        })->toArray();
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('print_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => route('menu-group.print', $record))
                        ->openUrlInNewTab(),
                    Action::make('export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn($record) => route('menu-group.export', $record))
                        ->openUrlInNewTab(),
                ])
                    ->button()
                    ->label('Tindakan')
                    ->icon(Heroicon::PaperClip),
                EditAction::make()
                    ->label('')
                    ->tooltip('Ubah'),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // Bulk Export Excel Action
                    BulkAction::make('bulk_export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();
                            $url = route('menu-group.bulk-export', ['ids' => implode(',', $ids)]);

                            // Redirect to download URL
                            return redirect($url);
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_export_pdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-printer')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();
                            $url = route('menu-group.bulk-print', ['ids' => implode(',', $ids)]);

                            // Redirect to download URL
                            return redirect($url);
                        })
                        ->deselectRecordsAfterCompletion(),

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMenuGroups::route('/'),
        ];
    }
}
