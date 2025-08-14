<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use App\Filament\Resources\MemberResource\RelationManagers;

class MemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';
    protected static ?string $navigationLabel = 'Member';
    protected static ?string $pluralModelLabel = 'Data Member';

    protected static function isViewPage(): bool
    {
        return request()->routeIs('filament.admin.resources.members.view');
    }

    public static function form(Form $form): Form
    {
        $isView = self::isViewPage();

        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make(3)->visible($isView)
                    ->schema([
                        Section::make('Detail Member')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('nama_lengkap_placeholder')
                                    ->label('Nama Lengkap')
                                    ->content(fn($record) => $record?->pendaftar?->nama_lengkap ?? '-'),

                                \Filament\Forms\Components\Placeholder::make('username_placeholder')
                                    ->label('Username')
                                    ->content(fn($record) => $record->username),

                                \Filament\Forms\Components\Placeholder::make('email_placeholder')
                                    ->label('Email')
                                    ->content(fn($record) => $record->email),

                                \Filament\Forms\Components\Placeholder::make('instansi_placeholder')
                                    ->label('Asal Instansi')
                                    ->content(fn($record) => $record?->pendaftar?->asal_instansi ?? '-'),

                                \Filament\Forms\Components\Placeholder::make('notelp_placeholder')
                                    ->label('Nomor Telepon')
                                    ->content(fn($record) => $record?->pendaftar?->no_telepon ?? '-'),

                                \Filament\Forms\Components\Placeholder::make('usia_placeholder')
                                    ->label('Usia')
                                    ->content(fn($record) => $record?->pendaftar?->age ?? '-'),

                                \Filament\Forms\Components\Placeholder::make('created_at_placeholder')
                                    ->label('Registered at')
                                    ->content(fn($record) => optional($record->created_at)->format('d M Y')),

                                \Filament\Forms\Components\Placeholder::make('penyakit_placeholder')
                                    ->label('Riwayat Penyakit')
                                    ->content(fn($record) => $record?->pendaftar?->riwayat_penyakit ?? '-'),

                            ])
                            ->columns(2)
                            ->columnSpan(2),
                        Section::make('Foto')
                            ->schema([
                                \Filament\Forms\Components\ViewField::make('foto')
                                    ->view('components.member-foto')
                                    ->label(''),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Informasi Akun')
                    ->hidden($isView)
                    ->schema([
                        TextInput::make('username')->label('Username'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pendaftar.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pendaftar.no_telepon')
                    ->label('Nomor Telepon')
                    ->searchable(),
                // Tables\Columns\ImageColumn::make('foto')
                //     ->label('Foto')
                //     ->getStateUsing(function ($record) {
                //         $path = $record->pendaftar?->registrant_picture ?: 'images/dummy.png';
                //         return asset('storage/' . ltrim($path, '/'));
                //     })
                //     ->square()
                //     ->defaultImageUrl(asset('storage/images/dummy.png')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Data'),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'member')
            ->with('pendaftar');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PendaftarEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
            'view' => Pages\ViewMember::route('/{record}'),
        ];
    }
}
