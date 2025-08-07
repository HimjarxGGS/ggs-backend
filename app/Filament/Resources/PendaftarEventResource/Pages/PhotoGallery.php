<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use Filament\Resources\Pages\Page;

class PhotoGallery extends Page
{
    protected static string $resource = PendaftarEventResource::class;

    protected static string $view = 'filament.resources.pendaftar-event-resource.pages.photo-gallery';

    public Event $event;
    public function mount(): void
    {
        $this->event = Event::findOrFail(request()->query('event_id'));
    }

    protected function getRegistrants()
    {
        return $this->event
            ->pendaftarEvents()
            ->with('pendaftar.user')
            ->get()
            ->pluck('pendaftar');
    }

    public function getTitle(): string
    {
        return 'Foto Pendaftar';
    }
}
