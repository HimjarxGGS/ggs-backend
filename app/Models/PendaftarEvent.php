<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftarEvent extends Model
{
    //
    // app/Models/PendaftarEvent.php

    protected $primaryKey = 'id';

    protected $with = ['pendaftar'];

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class);
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
        return asset('storage/' . $this->bukti_share);
    }

    public function getBuktiPaymentUrlAttribute(): string
    {
        return asset('storage/' . $this->bukti_payment);
    }
}
