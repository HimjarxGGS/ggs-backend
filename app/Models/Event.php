<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    //
    public function dokumentasi(): HasMany
    {
        return $this->hasMany(DokumentasiEvent::class);
    }
    
    public function pendaftarEvents(): HasMany
    {
        return $this->hasMany(PendaftarEvent::class);
    }

    // Optional: shortcut to count verified participants
    public function getVerifiedPendaftarCountAttribute(): int
    {
        return $this->pendaftarEvents()->where('status', 'verified')->count();
    }
}
