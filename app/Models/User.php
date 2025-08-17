<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // app/Models/User.php

    public function approvedPendaftarEvents(): HasMany
    {
        return $this->hasMany(PendaftarEvent::class, 'approved_by');
    }

    public function pendaftar(): HasOne
    {
        return $this->hasOne(Pendaftar::class);
    }

    public function getIsGuestAttribute(): bool
    {
        return is_null($this->user_id);
    }

        public function pendaftarEvents(): HasManyThrough
    {
        return $this->hasManyThrough(PendaftarEvent::class, Pendaftar::class);
    }
}
