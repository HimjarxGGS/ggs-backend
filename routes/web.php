<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventPhotoController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['web','auth']) // replace with your Filament admin middleware if you want
    ->prefix('admin') // optional, depending on how your Filament routes are set up
    ->group(function () {
        Route::get('/events/{event}/photos/download-all', [EventPhotoController::class, 'downloadAll'])
            ->name('admin.events.photos.downloadAll');

        Route::get('/events/{event}/photos/download', [EventPhotoController::class, 'downloadRegistrantPhoto'])
            ->name('admin.events.registrant-photo');
    });