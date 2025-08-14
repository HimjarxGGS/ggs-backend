<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PendaftarEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'pendaftarEvents';

    protected static ?string $title = 'Riwayat Event';

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('status')
            // ->modifyQueryUsing(function (Builder $query) {
            //     $query->whereIn('status', ['Active', 'Finished']);
            // })
            ->columns([
                Tables\Columns\TextColumn::make('event.event_date')
                    ->label('Waktu Event')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Nama Event')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('event.status')
                    ->label('Status Event')
                    ->colors([
                        'warning' => 'active',
                        'success' => 'finished',
                    ]),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
