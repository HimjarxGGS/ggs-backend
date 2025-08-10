<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;

class MemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
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
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->disabled($isView),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled($isView),

                        DateTimePicker::make('created_at')
                            ->label('Tanggal Bergabung')
                            ->disabled($isView),
                    ]),

                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('pendaftar.no_telepon')
                            ->label('Nomor Telepon')
                            ->formatStateUsing(fn($record) => $record?->pendaftar?->no_telepon)
                            ->disabled($isView),

                        TextInput::make('pendaftar.asal_instansi')
                            ->label('Asal Instansi')
                            ->formatStateUsing(fn($record) => $record?->pendaftar?->asal_instansi)
                            ->disabled($isView),

                        TextInput::make('pendaftar.date_of_birth')
                            ->label('Tanggal Lahir')
                            ->formatStateUsing(
                                fn($record) =>
                                optional($record?->pendaftar?->date_of_birth)->format('d-m-Y')
                            )
                            ->disabled($isView),


                        TextInput::make('usia')
                            ->label('Usia')
                            ->formatStateUsing(
                                fn($record) =>
                                $record?->pendaftar?->age ?? '-'
                            )
                            ->disabled(),


                        TextInput::make('pendaftar.riwayat_penyakit')
                            ->label('Riwayat Penyakit')
                            ->formatStateUsing(fn($record) => $record?->pendaftar?->riwayat_penyakit)
                            ->disabled($isView),

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
                Tables\Columns\ImageColumn::make('foto')
                    ->disk('public')
                    ->getStateUsing(fn($record) => $record->pendaftar?->registrant_picture)
                    ->square()

            ])
            ->filters([
                // 
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Data'),
                // Tables\Actions\EditAction::make()->label('Ubah Data'),
            ])
            ->bulkActions([
                // 
            ]);
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
            // 
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
