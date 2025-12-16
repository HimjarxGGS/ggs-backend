<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PendaftarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'status',
        'approved_by'
    ];
    protected $primaryKey = 'id';

    protected $with = ['pendaftar'];

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class, 'pendaftar_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Optional: dynamic URL for displaying bukti images
    public function getBuktiShareUrlAttribute(): string
    {
        $path = $this->bukti_share;
        if (! $path) {
            return "";
        }

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

        return "";
        // return asset('storage/' . $this->bukti_share);
    }

    public function getBuktiPaymentUrlAttribute(): string
    {
        return asset('storage/' . $this->bukti_payment);
    }
}
