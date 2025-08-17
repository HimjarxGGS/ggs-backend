<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumentasiEvent extends Model
{
    use SoftDeletes;
    //
    public function event() : BelongsTo{
        return $this->belongsTo(Event::class);
    }
}
