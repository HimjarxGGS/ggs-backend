<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumentasiEvent extends Model
{
    use SoftDeletes;
    protected $table = 'dokumentasi_event';
    public $timestamps = false; 
    protected $fillable = ['event_id', 'image'];
    //
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
