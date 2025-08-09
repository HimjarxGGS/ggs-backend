<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftarEventResource\Pages;
use App\Models\PendaftarEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendaftarEventResource extends Resource
{
    protected static ?string $model = PendaftarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Pendaftar Event';
    protected static ?string $pluralModelLabel = 'Pendaftar Events';
    protected static ?string $slug = 'pendaftar-events';

    // permission surface — disable these if you don't want create/edit/delete in Filament panel
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Minimal, generic table. The List page now overrides the table() completely,
     * but keep this here to avoid surprises if other pages rely on the resource default.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->query(static::$model::query())
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                // lightweight default columns — keep these generic
                TextColumn::make('pendaftar.nama_lengkap')->label('Nama')->limit(30)->sortable(),
                TextColumn::make('pendaftar.email')->label('Email')->limit(35)->sortable(),
                TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            ])
            ->filters([
                // keep resource-level filters minimal; page can replace/add as needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
            ])
            ->headerActions([
                // leave empty — page should own header actions like "Folder Foto"
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarEvents::route('/'),
            'photo-gallery' => Pages\PhotoGallery::route('/photo-gallery'),
        ];
    }
}
