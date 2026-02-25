<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasFactory;
    use HasApiTokens;
    use InteractsWithMedia;
    use Notifiable;
    use HasRolesAndAbilities;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_access_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'api_access_enabled' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(\App\Models\User\UserProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Models\User\UserAddress::class);
    }

    public function customerGroups(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User\CustomerGroup::class, 'customer_group_user')
            ->withTimestamps();
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(\App\Models\User\UserTrackingEvent::class);
    }
}
