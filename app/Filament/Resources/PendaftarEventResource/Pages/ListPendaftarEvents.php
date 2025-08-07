<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use App\Models\PendaftarEvent;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListPendaftarEvents extends ListRecords
{
    protected static string $resource = PendaftarEventResource::class;

    public function isEventList(): bool
    {
        return ! request()->has('event_id');
    }

    public function getTitle(): string
    {
        if ($this->isEventList()) {
            return 'Data Event';
        }

        $event = Event::find(request()->query('event_id'));
        return 'Data Pendaftar ' . ($event?->name ?? 'Unknown Event');
    }
}
