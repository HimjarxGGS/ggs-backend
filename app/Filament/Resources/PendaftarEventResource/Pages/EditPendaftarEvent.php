<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftarEvent extends EditRecord
{
    protected static string $resource = PendaftarEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
