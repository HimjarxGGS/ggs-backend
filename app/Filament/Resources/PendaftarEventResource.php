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
        return true;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarEvents::route('/'),
            'edit' => Pages\ViewPendaftarEvent::route('/{record}/edit'),
            'photo-gallery' => Pages\PhotoGallery::route('/photo-gallery'),
        ];
    }
}
