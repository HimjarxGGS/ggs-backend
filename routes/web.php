<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventPhotoController;
use App\Models\PendaftarEvent;

// Route::get('/', function () {
//     return view('welcome');
// });

$adminPrefix = config('filament.path', 'admin');
$filamentGuard = config('filament.auth.guard') ?? 'web';

Route::middleware(['web', "auth:{$filamentGuard}"]) // replace with your Filament admin middleware if you want
    ->prefix($adminPrefix) // optional, depending on how your Filament routes are set up
    ->group(function () {
        Route::get('/events/{event}/photos/download-all', [EventPhotoController::class, 'downloadAll'])
            ->name('admin.events.photos.downloadAll');

        Route::get('/events/{event}/photos/download', [EventPhotoController::class, 'downloadRegistrantPhoto'])
            ->name('admin.events.registrant-photo');



        Route::get('/pending-registrants-count', function () {
            $count = PendaftarEvent::where('status', 'pending')->count();
            $color = $count > 10 ? 'warning' : 'primary';
            return response()->json(['count' => $count, 'color' => $color]);
        })->name('filament.pending-registrants-count');
    });
