<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use App\Models\PendaftarEvent;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class ListPendaftarEvents extends ListRecords
{
    protected static string $resource = PendaftarEventResource::class;

    // persisted in URL as ?event_id=123
    public ?int $eventId = null;
    public ?Event $event = null;

    protected array $queryString = [
        'eventId' => ['as' => 'event_id', 'except' => null],
    ];

    public function mount(): void
    {
        // prefer query string; fallback to request()
        $this->eventId = $this->eventId ?? (request()->query('event_id') ? (int) request()->query('event_id') : null);
        $this->event = $this->eventId ? Event::find($this->eventId) : null;
    }

    public function isEventList(): bool
    {
        return $this->eventId === null;
    }

    public function getTitle(): string
    {
        return $this->isEventList() ? 'Data Event' : 'Data Pendaftar ' . ($this->event?->name ?? 'Unknown Event');
    }

    public function getBreadcrumbs(): array
    {
        return $this->isEventList()
            ? [route('filament.admin.resources.pendaftar-events.index') => 'Event']
            : [
                route('filament.admin.resources.pendaftar-events.index') => 'Event',
                route('filament.admin.resources.pendaftar-events.index', ['event_id' => $this->eventId]) => 'Pendaftar',
            ];
    }


    public function table(Table $table): Table
    {
        if ($this->isEventList()) {
            return $table
                ->query(Event::query()->withCount('pendaftarEvents'))
                ->columns([
                    TextColumn::make('name')->label('Event')->sortable()->searchable()->lineClamp(2)->wrap(),
                    TextColumn::make('event_date')->label('Date')->date()->sortable(),
                    TextColumn::make('status')->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'finished' => 'success',
                            'active' => 'warning',
                            default => 'secondary',
                        })->label('Status')->sortable(),
                    TextColumn::make('pendaftar_events_count')
                        ->label('Registrants')
                        ->counts('pendaftarEvents')
                        ->sortable(),
                ])
                ->filters([
                    SelectFilter::make('status')->label('Status')->options([
                        'active' => 'Active',
                        'finished' => 'Finished',
                    ]),
                ])
                ->actions([
                    Action::make('viewRegistrants')
                        ->label('Lihat Data Pendaftar')
                        ->icon('heroicon-o-user-group')
                        ->url(fn($record) => route('filament.admin.resources.pendaftar-events.index', ['event_id' => $record->id])),
                ]);
        }

        // Registrants table
        return $table
            ->query(PendaftarEvent::query()->where('event_id', $this->eventId)->with('pendaftar'))
            ->columns([
                TextColumn::make('pendaftar.nama_lengkap')->label('Nama')->sortable()->searchable(),
                TextColumn::make('pendaftar.email')->label('Email')->sortable()->searchable(),
                TextColumn::make('created_at')->date()->label("Tanggal")->sortable()->searchable(),
                TextColumn::make('approvedBy.name')->label("Approved By")->searchable(),
                TextColumn::make('pendaftar.user_id')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => $state ? 'Member' : 'Guest')
                    ->badge()
                    ->color(fn($state) => $state === 'Member' ? 'success' : 'secondary')
                    ->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        default => 'secondary',
                    })->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options([
                    'pending' => 'Pending',
                    'verified' => 'Verified',
                ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat Data')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.pendaftar-events.detail-pendaftar', ['record' => $record->id])),

            ])
            ->headerActions([
                Action::make('photoFolders')
                    ->label('Folder Foto')
                    ->icon('heroicon-o-photo')
                    ->visible(fn(): bool => $this->event?->need_registrant_picture === 'ya')
                    ->url(fn(): string => PhotoGallery::getUrl([
                        'event_id' => $this->eventId,
                    ])),
            ]);
    }
}
