<?php

namespace App\Filament\Resources\MenuGroups;

use App\Filament\Resources\MenuGroups\Pages\ManageMenuGroups;
use App\Models\MenuGroup;
use App\Models\User;
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MenuGroupResource extends Resource
{
    protected static ?string $model = MenuGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = "Hitung Kebutuhan";

    protected static ?string $label = "Hitung Kebutuhan Dapur";

    // public static function getNavigationBadge(): ?string
    // {
    //     return "✨";
    // }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),

                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required()
                    ->prefix('Hari ke')
                    ->placeholder('Menu Hari ke'),

                Select::make('sppg_id')
                    ->relationship('sppg', 'name')
                    ->label('Nama SPPG'),

                TextInput::make('requested_portions')
                    ->label('Jumlah Porsi')
                    ->numeric()
                    ->required(),

                Hidden::make('created_by')
                    ->default(Auth::id()),

                Repeater::make('recipes')
                    ->table([
                        TableColumn::make('Menu Masakan')
                    ])
                    ->relationship()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('recipe_id')
                            ->label('Menu Masakan')
                            ->relationship('recipe', 'name')
                            ->required(),
                    ])
                    ->label('Daftar Menu')
                    ->columnSpanFull()
                    ->required(),
            ])->columns(2);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->default('-'),

                TextEntry::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime(),

                ViewEntry::make('kalkulasi_bahan')
                    ->label('Kalkulasi Bahan')
                    ->view('infolists.menu-group')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->searchable()
                    ->label('Tanggal')
                    ->date(),

                TextColumn::make('name')
                    ->searchable()
                    ->prefix('Hari ke - ')
                    ->label('Hari'),

                TextColumn::make('sppg.name')
                    ->searchable()
                    ->label('SPPG')
                    ->formatStateUsing(fn($state) => strtoupper($state)),


                TextColumn::make('recipes_portions')
                    ->label('Menu Makanan')
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return $record->recipes->map(function ($menuRecipe) {
                            return "{$menuRecipe->recipe->name}";
                        })->toArray();
                    }),

                TextColumn::make('requested_portions')
                    ->label('Porsi'),
                // ->listWithLineBreaks()
                // ->getStateUsing(function ($record) {
                //     return $record->recipes->map(function ($menuRecipe) {
                //         return "{$menuRecipe->requested_portions} porsi";
                //     })->toArray();
                // }),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('created_by')
                    ->label('Dibuat Oleh')
                    ->relationship('createdBy', 'name')
                    // ->searchable()
                    ->preload(),
                SelectFilter::make('sppg.name')
                    ->label('SPPG')
                    ->relationship('sppg', 'name')
                    // ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
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
                ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat'),
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
