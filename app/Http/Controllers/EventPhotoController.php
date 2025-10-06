<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class EventPhotoController extends Controller
{
    //
    use AuthorizesRequests, ValidatesRequests;

    // Download all photos (zip)

    public function downloadAll(Event $event, Request $request)
    {
        if (! filament()->auth()->check()) {
            abort(403);
        }

        $registrants = $event
            ->pendaftarEvents()
            ->with('pendaftar')
            ->get()
            ->pluck('pendaftar')
            ->filter(fn($p) => ! empty($p->registrant_picture));

        if ($registrants->isEmpty()) {
            abort(404, 'No photos to download.');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'photos_') . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
            abort(500, 'Failed to create zip file.');
        }

        foreach ($registrants as $registrant) {
            $path = $registrant->registrant_picture;
            $filename = $this->formatRegistrantFilename($registrant, $path);
            // $filename = $this->generatePhotoFilename($registrant);

            if (Str::startsWith($path, ['http://', 'https://'])) {
                $parsed = parse_url($path);

                // Local domain → read file directly
                if (in_array($parsed['host'] ?? '', ['127.0.0.1', 'localhost', 'yourdomain.com'])) {
                    $relativePath = ltrim($parsed['path'], '/');
                    if (Storage::disk('public')->exists($relativePath)) {
                        $zip->addFile(Storage::disk('public')->path($relativePath), $filename);
                    } elseif (file_exists(public_path($relativePath))) {
                        $zip->addFile(public_path($relativePath), $filename);
                    }
                } else {
                    // Remote URL
                    try {
                        $resp = Http::timeout(10)->get($path);
                        if ($resp->successful()) {
                            $zip->addFromString($filename, $resp->body());
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
                continue;
            }

            // Local storage
            if (Storage::disk('public')->exists($path)) {
                $zip->addFile(Storage::disk('public')->path($path), $filename);
                continue;
            }

            // Fallback public/ path
            if (file_exists(public_path($path))) {
                $zip->addFile(public_path($path), $filename);
            }
        }

        $zip->close();
        return response()->download($tempFile, "event-{$event->name}-photos.zip")->deleteFileAfterSend(true);
    }

    public function downloadRegistrantPhoto(Event $event, Request $request)
    {
        if (! filament()->auth()->check()) {
            abort(403, "Forbidden Access");
        }

        $encoded = $request->query('path');
        $registrantId = $request->query('registrant_id');
        
        if (! $encoded) {
            abort(400, "Missing parameter");
        }
        if (! $registrantId) {
            abort(404, "Can't found registrant id");
        }
        $path = base64_decode($encoded);
        if (! $path) {
            abort(404, "Can't found path");
        }

        $registrant = $event->pendaftarEvents()
            ->with('pendaftar')
            ->get()
            ->pluck('pendaftar')
            ->firstWhere('id', $registrantId);

        if (! $registrant) {
            abort(403, "Forbidden registrant id");
        }
        $filename = $this->formatRegistrantFilename($registrant, $path);
        // $filename = $this->generatePhotoFilename($registrant);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $parsed = parse_url($path);

            if (in_array($parsed['host'] ?? '', ['127.0.0.1', 'localhost', 'yourdomain.com'])) {
                $relativePath = ltrim($parsed['path'], '/');
                if (Storage::disk('public')->exists($relativePath)) {
                    return response()->download(Storage::disk('public')->path($relativePath), $filename);
                } elseif (file_exists(public_path($relativePath))) {
                    return response()->download(public_path($relativePath), $filename);
                }
            } else {
                $resp = Http::timeout(10)->get($path);
                if (! $resp->successful()) {
                    abort(408, "Request Timeout");
                }
                $contentType = $resp->header('Content-Type', 'application/octet-stream');
                return response()->streamDownload(fn() => print($resp->body()), $filename, ['Content-Type' => $contentType]);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::download($path, $filename);
        }

        clearstatcache();
        dd(Storage::disk('public')->exists($path), file_exists($path), file_exists(public_path($path)), file_exists(url($path)), url($path), $filename);
        try{
            return response()->download(url($path), $filename);
        }catch(\Exception $e){
            abort(404, $e->getMessage());
        }

        // if (file_exists(public_path($path))) {
        //     return response()->download(url($path), $filename);
        // }
        
        
        abort(404, "File doesn't exists or not found");
    }

    
    protected function formatRegistrantFilename($registrant, $path)
    {
        // Safe version of registrant name
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $registrant->nama_lengkap);

        // Detect file extension from the path or URL
        $ext = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';

        // Format pattern: change here anytime you want
        return "{$safeName}.{$ext}";
    }
}
