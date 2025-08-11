<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Pendaftar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'date_of_birth',
        'email',
        'asal_instansi',
        'no_telepon',
        'riwayat_penyakit',
        'registrant_picture'
    ];
     // Accessor: $pendaftar->registrant_picture_url
     public function getRegistrantPictureUrlAttribute(): ?string
     {
         $path = $this->registrant_picture;
         if (! $path) {
             return null;
         }
 
         // full URL already
         if (Str::startsWith($path, ['http://', 'https://'])) {
             return $path;
         }
 
         // public disk (recommended)
         if (Storage::disk('public')->exists($path)) {
             return Storage::url($path);
         }
 
         // a path inside public/ (fallback)
         if (file_exists(public_path($path))) {
             return asset($path);
         }
 
         return null;
     }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftarEvents(): HasMany
    {
        return $this->hasMany(PendaftarEvent::class, 'pendaftar_id');
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->date_of_birth)->age,
        );
    }

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // public function pendaftarEvent() : BelongsTo
    // {
    //     return $this->belongsTo(PendaftarEvent::class);
    // }
}
