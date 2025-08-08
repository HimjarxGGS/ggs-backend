<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftarEventResource\Pages;
use App\Filament\Resources\PendaftarEventResource\Pages\ListPendaftarEvents;
use App\Filament\Resources\PendaftarEventResource\Pages\PhotoGallery;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\PendaftarEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class PendaftarEventResource extends Resource
{
    protected static ?string $model = PendaftarEvent::class;
    protected static bool   $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
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
        $isEventList = !request()->has('event_id');

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
                        TextColumn::make('event_date')->label('Date')->date()->sortable(),
                        TextColumn::make('status')->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'finished' => 'success',
                                'active' => 'warning',
                            })->label('Status')->sortable(),
                        TextColumn::make('pendaftar_events_count')
                            ->label('Registrants')
                            ->counts('pendaftarEvents')
                            ->sortable(),
                    ]
                    : [
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
                            })->sortable(),

                    ]
            )

            // Filters
            ->filters(
                $isEventList
                    ? [
                        SelectFilter::make('status')
                            ->label('Status')
                            ->options(['active' => 'Active', 'finished' => 'Finished']),
                    ]
                    : [
                        SelectFilter::make('status')
                            ->label('Status')
                            ->options(['pending' => 'Pending', 'verified' => 'Verified']),
                    ]
            )
            // Actions
            ->actions(
                $isEventList
                    ? [
                        Action::make('viewRegistrants')
                            ->label('Lihat Data Pendaftar')
                            ->icon('heroicon-o-user-group')
                            ->url(fn($record) => route(
                                'filament.admin.resources.pendaftar-events.index',
                                ['event_id' => $record->id],
                            )),
                    ]
                    : [
                        ViewAction::make()->label('Lihat Data'),
                        // Action::make('detail')
                        //     ->label('Lihat Data')
                        //     ->icon('heroicon-o-eye'),
                        // ->url(fn($record) => {}), // TODO to detail pendaftar
                    ]
            )->headerActions(
                $isEventList
                    ? []
                    : [
                        Action::make('photoFolders')
                            ->label('Folder Foto')
                            ->icon('heroicon-o-photo')
                            ->visible(fn(): bool => Event::where('need_registrant_picture', 'ya')->exists())
                            ->url(fn($record): string => PhotoGallery::getUrl([
                                'event_id' => request()->has('event_id'),
                            ])),
                    ]
            );
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarEvents::route('/'),
            'photo-gallery'   => Pages\PhotoGallery::route('/photo-gallery'),
        ];
    }
}
