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

            Action::make('Download Semua Foto')
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function () {
                    $this->downloadAll();
                    // Collect paths of all registrant photos
                    // $files = Pendaftar::where('id', )->whereNotNull('registrant_picture')
                    //     ->pluck('registrant_picture')
                    //     ->map(function ($path) {
                    //         return Storage::path($path); // Absolute path
                    //     });

                    // $zipFileName = 'registrant_photos.zip';
                    // $zipFilePath = storage_path("app/{$zipFileName}");

                    // $zip = new ZipArchive;
                    // if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    //     foreach ($files as $file) {
                    //         $zip->addFile($file, basename($file));
                    //     }
                    //     $zip->close();
                    // }

                    // return response()->download($zipFilePath)->deleteFileAfterSend(true);
                })
                ->color('primary')
        ];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('back'),
    //     ];
    // }

    public function downloadAll()
    {
        $zipFileName = 'registrant_photos.zip';
        $zipPath = storage_path("app/public/{$zipFileName}");
        // create a temporary file
        // $zipPath = storage_path('app/temp/pendaftar-photos-' . $this->event->name[10] . '.zip');
        // @unlink($zipPath);

        // $zip = new ZipArchive;
        // // $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // $zip->open($zipPath);

        // foreach ($this->getRegistrants() as $pendaftar) {
        //     $filePath = storage_path($pendaftar->registrant_picture);
        //     if (file_exists($filePath)) {
        //         // if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        //             $zip->addFile($filePath, $pendaftar->nama_lengkap . '_' . basename($filePath));
        //             // $zip->close();
        //         // }
        //     }
        // }


        // //         // add the file with a friendly name
        // // var_dump(asset('storage/images/ggs_logo.png'));
        // // $zip->addFile(asset('storage/images/ggs_logo.png'));
        if (Storage::disk('public')->exists('images/ggs_logo.png')) {
            Storage::download('images/ggs_logo.png');
            // abort(404, 'file not found.');
        }
        // $zip->close();
        return Storage::download('images/ggs_logo.png');
        // return response()
        //     ->download(storage_path('images/ggs_logo.png'));
            // ->download($zipPath, 'gallery-event-' . $this->event->id . '.zip')
            // ->deleteFileAfterSend(); 
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
