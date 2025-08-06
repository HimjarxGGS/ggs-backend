<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftar extends Model
{
    //
    public function user() : ?BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function pendaftarEvents() : HasMany{
        return $this->hasMany(Event::class);
    }
}
