<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $activeNavigationIcon = 'heroicon-s-star';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('pendaftarEvents'); 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Event')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('event_date')
                            ->label('Tanggal Event')
                            ->required()
                            ->minDate(now()),

                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'finished' => 'Finished',
                            ])
                            ->required(),

                        Forms\Components\Select::make('event_format')
                            ->label('Format Event')
                            ->options([
                                'online' => 'Online',
                                'offline' => 'Offline',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->required(),

                        Forms\Components\FileUpload::make('poster')
                            ->image()
                            ->directory('event-posters'),
                        Forms\Components\Select::make('need_registrant_picture')
                            ->label('Butuh Foto Pendaftar?')
                            ->options([
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                            ])
                            ->required()
                            ->default('tidak'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Event')
                    ->lineClamp(2)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'finished',
                        'success' => 'active',
                    ]),

                TextColumn::make('pendaftar_events_count')
                    ->label('Jumlah Pendaftar')
                    ->counts('pendaftarEvents')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                //
            ]);
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
            'index' => Pages\ListEvents::route('/'),
            // 'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
