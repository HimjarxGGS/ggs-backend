<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use App\Models\Pendaftar;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PhotoGallery extends Page
{
    protected static string $resource = PendaftarEventResource::class;

    protected static string $view = 'filament.resources.pendaftar-event-resource.pages.photo-gallery';
    public string $search = '';

    public Event $event;
    public function mount(): void
    {
        $this->event = Event::findOrFail(request()->query('event_id'));
    }

    protected function getRegistrants()
    {
        // Load only those with pictures
        $query = $this->event
            ->pendaftarEvents()
            ->with('pendaftar')
            ->get()
            ->pluck('pendaftar')
            ->filter(fn($p) => $p->registrant_picture);

        if (filled($this->search)) {
            $term = strtolower($this->search);
            $query = $query->filter(
                fn($p) =>
                Str::contains(strtolower($p->nama_lengkap), $term) ||
                    Str::contains(strtolower(basename($p->registrant_picture)), $term)
            );
        }

        return $query;
    }


    protected function getHeaderActions(): array
    {
        return [

            // add search action
            Action::make('Download Semua Foto')
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function () {
                    $this->downloadAll();
                
                })
                ->color('primary')
        ];
    }

    

    public function downloadAll()
    {
        $zipFileName = $this->event->name .'_registrant_photos.zip';

        // rename the picture accordingly to each pendaftar name
        // make it to zip
        // download
        
    }

    public function getTitle(): string
    {
        return 'Foto Pendaftar';
    }

    // public function getBreadcrumb(): ?string
    // {
    //     return 'Kembali ke Event'; // This appears in the breadcrumb
    // }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pendaftar-events.index') => 'Event',
            route('filament.admin.resources.pendaftar-events.index', ['event_id' => request()->query('event_id')]) => 'Pendaftar',
            route('filament.admin.resources.pendaftar-events.photo-gallery') => 'Foto pendaftar',
        ];
    }
}
