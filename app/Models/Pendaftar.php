<?php

namespace App\Models;

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

    public function user() : BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftarEvents() : HasMany
    {
        return $this->hasMany(PendaftarEvent::class, 'pendaftar_id');
    }

    // public function pendaftarEvent() : BelongsTo
    // {
    //     return $this->belongsTo(PendaftarEvent::class);
    // }
}