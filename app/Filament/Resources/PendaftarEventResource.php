<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftarEventResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\PendaftarEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;


class PendaftarEventResource extends Resource
{
    protected static ?string $model = PendaftarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/PendaftarEventResource.php


    public static function getEloquentQuery(): Builder
    {
        // Base pivot query grouped by event_id
        return parent::getEloquentQuery()
            ->select('event_id')
            ->selectRaw('COUNT(*) AS pendaftar_count')
            ->groupBy('event_id')
            ->with('event'); // eager-load event relation
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }


    public static function canDelete(Model $record): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('event.name')
                ->label('Event')
                ->sortable()
                ->searchable(),

            TextColumn::make('event.date')
                ->label('Date')
                ->date()
                ->sortable(),

            TextColumn::make('event.status')
                ->label('Status')
                ->sortable(),

            TextColumn::make('pendaftar_count')
                ->label('Registrants')
                ->sortable(),
        ])
        // We'll add the "View Registrants" action in a moment
        ->filters([
            // e.g. Filter by status:
            Tables\Filters\SelectFilter::make('event.status')
                ->options([
                    'pending'  => 'Pending',
                    'verified' => 'Verified',
                ]),
        ])
        ->actions([
            Action::make('viewRegistrants')
                ->label('View Registrants')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route(
                    'filament.admin.resources.pendaftar-event-resource.pages.list-pendaftar-events',
                    ['event_id' => $record->event_id]
                )),
        ])
        ->searchable();
        // return $table
        //     ->columns([
        //         //
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->actions([
        //         Tables\Actions\EditAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarEvents::route('/'),
            'create' => Pages\CreatePendaftarEvent::route('/create'),
            'edit' => Pages\EditPendaftarEvent::route('/{record}/edit'),
        ];
    }
}
