<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    ];

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
