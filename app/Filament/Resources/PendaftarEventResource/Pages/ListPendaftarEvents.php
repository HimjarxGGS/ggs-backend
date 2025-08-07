<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use App\Models\PendaftarEvent;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListPendaftarEvents extends ListRecords
{
    protected static string $resource = PendaftarEventResource::class;

    public function isEventList(): bool
    {
        return ! request()->has('event_id');
    }

    protected function getTableQuery(): Builder
    {
        if ($this->isEventList()) {
            // List all events, even those without registrants
            return Event::query()->withCount('pendaftarEvents');
        }


        // List only registrations for one event
        return PendaftarEvent::query()
            ->where('event_id', request()->query('event_id'))
            ->with(['pendaftar', 'event']);
    }


    protected function getTableColumns(): array
    {
        if ($this->isEventList()) {
            return [
                TextColumn::make('name')->lineClamp(2)->wrap()
                    ->label('Event')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    
                    'finished' => 'warning',
                    'active' => 'success',
                    
                })->label('Status'),

                TextColumn::make('pendaftar_events_count')
                    ->label('Registrants')
                    ->counts('pendaftarEvents')
                    ->sortable(),
            ];
        }

        return [
            TextColumn::make('pendaftar.nama_lengkap')
                ->label('Nama Pendaftar')
                ->sortable()
                ->searchable(),

            TextColumn::make('pendaftar.email')
                ->label('Email')
                ->sortable()
                ->searchable(),

            TextColumn::make('status')
                ->label('Registration Status')
                ->badge(),
        ];
    }

    protected function getTableActions(): array
    {
        if ($this->isEventList()) {
            return [
                Action::make('viewRegistrants')
                    ->label('View Registrants')
                    ->icon('heroicon-o-user-group')
                    ->url(fn ($record) => route(
                        'filament.admin.resources.pendaftar-events.index',
                        ['event_id' => $record->id],
                    )),
            ];
        }

        return [
            Action::make('back')
                ->label('← Back to Events')
                ->url(route('filament.admin.resources.pendaftar-events.index')),
        ];
    }

    protected function getTableFilters(): array
    {
        if ($this->isEventList()) {
            return [
                SelectFilter::make('status')
                    ->label('Event Status')
                    ->options([
                        'active'   => 'Active',
                        'finished' => 'Finished',
                    ]),
            ];
        }

        return [
            SelectFilter::make('status')
                ->label('Registration Status')
                ->options([
                    'pending'  => 'Pending',
                    'verified' => 'Verified',
                ]),
        ];
    }

    public function getTitle(): string
    {
        if ($this->isEventList()) {
            return 'Data Event';
        }

        $event = Event::find(request()->query('event_id'));
        return 'Data Pendaftar ' . ($event?->name ?? 'Unknown Event');
    }
}
