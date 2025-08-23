<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'title',
        'content',
        'img',
        'slug',
        'tag',
        'author',
        'pic',
    ];

    public function categories() : BelongsToMany{
        return $this->belongsToMany(Category::class);
    }

    public function createdBy() : BelongsTo{
        return $this->belongsTo(User::class, 'pic');
    }

    protected $casts = [
        'tag' => 'array',
    ];
}
