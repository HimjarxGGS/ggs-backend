<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Models\Event;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
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
            $query = $query->filter(fn($p) =>
                Str::contains(strtolower($p->nama_lengkap), $term) ||
                Str::contains(strtolower(basename($p->registrant_picture)), $term)
            );
        }

        return $query;
    }


    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('back'),
    //     ];
    // }

    public function downloadAll()
    {
        // create a temporary file
        $zipPath = storage_path('app/temp/pendaftar-photos-'.$this->event->id.'.zip');
        @unlink($zipPath);

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE);

        foreach ($this->getRegistrants() as $pendaftar) {
            $filePath = storage_path('app/'.$pendaftar->registrant_picture);
            if (file_exists($filePath)) {
                // add the file with a friendly name
                $zip->addFile($filePath, $pendaftar->nama_lengkap . '_' . basename($filePath));
            }
        }

        $zip->close();

        return response()
            ->download($zipPath, 'gallery-event-'.$this->event->id.'.zip')
            ->deleteFileAfterSend();
    }

    public function getTitle(): string
    {
        return 'Foto Pendaftar';
    }
}
