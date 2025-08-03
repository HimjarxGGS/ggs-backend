<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    //
    protected $fillable = [
        'tite',
        'content',
        'img',
        'slug',
        'tag',
        'author',
    ];

    public function categories() : BelongsToMany{
        return $this->belongsToMany(Category::class);
    }

    public function pic() : BelongsTo{
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'tag' => 'array',
    ];
}
