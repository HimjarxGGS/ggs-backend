<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }

    //
    // public function mount($record): void
    // {
    //     parent::mount($record);
    //     $user = \App\Models\User::findOrFail($record);
    //     dd($user->pendaftar);
    // }
}
