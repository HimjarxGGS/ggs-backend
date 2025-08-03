<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    //
    public function Blogs() : BelongsToMany{
        return $this->belongsToMany(Blog::class);
    }
}
