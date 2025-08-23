<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'status',
        'event_format',
        'location',
        'poster',
        'need_registrant_picture',
    ];


    public function dokumentasi(): HasMany
    {
        return $this->hasMany(DokumentasiEvent::class);
    }

    public function pendaftarEvents(): HasMany
    {
        return $this->hasMany(PendaftarEvent::class);
    }

    public function getVerifiedPendaftarCountAttribute(): int
    {
        return $this->pendaftarEvents()->where('status', 'verified')->count();
    }

    public function dokumentasiEvents()
    {
        return $this->hasMany(\App\Models\DokumentasiEvent::class);
    }
}
