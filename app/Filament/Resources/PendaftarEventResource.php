<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftarEventResource\Pages;
use App\Filament\Resources\PendaftarEventResource\Pages\ListPendaftarEvents;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\PendaftarEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class PendaftarEventResource extends Resource
{
    protected static ?string $model = PendaftarEvent::class;

    protected static bool   $shouldRegisterNavigation = true;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


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

    public static function table(Table $table): Table
    {
        $isEventList = ! request()->has('event_id');

        // Swap the base query
        $query = $isEventList
            ? Event::query()->withCount('pendaftarEvents')
            : PendaftarEvent::query()
            ->where('event_id', request()->query('event_id'))
            ->with('pendaftar');

        return $table
            ->query($query)

            // Columns
            ->columns(
                $isEventList
                    ? [
                        TextColumn::make('name')->label('Event')->sortable()->searchable()->lineClamp(2)->wrap(),
                        TextColumn::make('date')->label('Date')->date()->sortable(),
                        TextColumn::make('status')->badge()
                        ->color(fn(string $state): string => match ($state) {
                            
                            'finished' => 'warning',
                            'active' => 'success',
                            
                        })->label('Status')->sortable(),
                        TextColumn::make('pendaftar_events_count')
                            ->label('Registrants')
                            ->counts('pendaftarEvents')
                            ->sortable(),
                    ]
                    : [
                        TextColumn::make('pendaftar.nama_lengkap')->label('Nama')->sortable()->searchable(),
                        TextColumn::make('pendaftar.email')->label('Email')->sortable()->searchable(),
                        TextColumn::make('status')->label('Registration Status')->badge(),
                    ]
            )

            // Filters
            ->filters(
                $isEventList
                    ? [
                        SelectFilter::make('status')
                            ->label('Event Status')
                            ->options(['active' => 'Active', 'finished' => 'Finished']),
                    ]
                    : [
                        SelectFilter::make('status')
                            ->label('Registration Status')
                            ->options(['pending' => 'Pending', 'verified' => 'Verified']),
                    ]
            )

            // Actions
            ->actions(
                $isEventList
                    ? [
                        Action::make('viewRegistrants')
                            ->label('View Registrants')
                            ->icon('heroicon-o-user-group')
                            ->url(fn($record) => route(
                                'filament.admin.resources.pendaftar-events.index',
                                ['event_id' => $record->id],
                            )),
                    ]
                    : [
                        Action::make('back')
                            ->label('← Back to Events')
                            ->url(route('filament.admin.resources.pendaftar-events.index')),
                    ]
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarEvents::route('/'),
        ];
    }
}
