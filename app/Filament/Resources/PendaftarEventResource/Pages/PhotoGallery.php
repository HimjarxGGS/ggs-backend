<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use App\Models\Pendaftar;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
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

    protected $listeners = [
        'searchUpdated' => 'setSearchFromEvent',
    ];


    #[On('searchUpdated')]
    public function setSearchFromEvent($value)
    {
        $this->search = $value;
    }


    protected function getRegistrants()
    {

        $query = $this->event
            ->pendaftarEvents()
            ->with('pendaftar');

        if (filled($this->search)) {
            $term = '%' . strtolower($this->search) . '%';
            $query->whereHas('pendaftar', function ($q) use ($term) {
                // note: lower() depends on DB collation; this is safer for case-insensitive match
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(registrant_picture) LIKE ?', [$term]);
            });
        }

        // pluck the related model ( only those with pictures )
        return $query
            ->get()
            ->pluck('pendaftar')
            ->filter(fn($p) => $p->registrant_picture)
            ->all();
    }


    protected function getHeaderActions(): array
    {
        return [

            //TODO add search action
            Action::make('search-inline')
                ->view('filament.resources.pendaftar-event-resource.actions.search-inline'),

            // direct GET to route to trigger browser download
            Action::make('download-all')
                ->label('Download Semua Foto')
                ->icon('heroicon-m-arrow-down-tray')
                ->url(fn() => route('admin.events.photos.downloadAll', ['event' => $this->event->id]))
                ->openUrlInNewTab(), // optional, so it triggers separate request
       
        ];
    }


    public function getTitle(): string
    {
        return 'Foto Pendaftar';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pendaftar-events.index') => 'Event',
            route('filament.admin.resources.pendaftar-events.index', ['event_id' => $this->event->id]) => 'Data Pendaftar',
            route('filament.admin.resources.pendaftar-events.photo-gallery') => 'Foto Pendaftar',
        ];
    }
}
