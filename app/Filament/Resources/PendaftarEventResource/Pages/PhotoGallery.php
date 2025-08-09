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

    // protected function getRegistrants()
    // {
    //     // Load only those with pictures
    //     $query = $this->event
    //         ->pendaftarEvents()
    //         ->with('pendaftar')
    //         ->get()
    //         ->pluck('pendaftar')
    //         ->filter(fn($p) => $p->registrant_picture);

    //     if (filled($this->search)) {
    //         $term = strtolower($this->search);
    //         $query = $query->filter(
    //             fn($p) =>
    //             Str::contains(strtolower($p->nama_lengkap), $term) ||
    //                 Str::contains(strtolower(basename($p->registrant_picture)), $term)
    //         );
    //     }

    //     return $query;
    // }

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
        // $zipFileName = $this->event->name . '_registrant_photos.zip';

        // rename the picture accordingly to each pendaftar name
        // make it to zip
        // download

        // dd($this->getRegistrants());
        $paths = $this->getRegistrants()->pluck('registrant_picture')->filter()->values();

        if ($paths->isEmpty()) {
            $this->notify('warning', 'Tidak ada foto untuk diunduh.');
            return;
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'photos') . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
            $this->notify('danger', 'Gagal membuat file zip.');
            return;
        }

        foreach ($paths as $path) {
            // if DB stores full URLs, you might need to fetch by HTTP — prefer storing disk paths
            if (Storage::exists($path)) {
                $contents = Storage::disk('public')->get($path);
                $zip->addFromString(basename($path), $contents);
            } else {
                // attempt to add by URL if the DB stores absolute URLs
                try {
                    $contents = file_get_contents($path);
                    if ($contents !== false) {
                        $zip->addFromString(basename($path), $contents);
                    }
                } catch (\Throwable $e) {
                    // skip broken files
                }
            }
        }

        $zip->close();

        return response()->download($tempFile, "event-{$this->event->id}-photos.zip")->deleteFileAfterSend(true);
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
