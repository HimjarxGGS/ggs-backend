<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PendaftarEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'pendaftarEvents';

    protected static ?string $title = 'Riwayat Event';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                // Mengambil data dari relasi 'event' di model PendaftarEvent
                Tables\Columns\TextColumn::make('event.waktu_event')
                    ->label('Waktu Event')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Nama Event')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Finished',
                        'warning' => 'Pending',
                    ]),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}