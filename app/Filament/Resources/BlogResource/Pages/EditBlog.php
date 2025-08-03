<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use App\Models\Blog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->after(function (Blog $record) {
                // delete single
                if ($record->img) {
                   Storage::disk('public')->delete($record->img);
                }
                // delete multiple
                if ($record->galery) {
                   foreach ($record->galery as $ph) Storage::disk('public')->delete($ph);
                }
             }),
        ];
    }
}
