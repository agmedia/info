<?php

namespace App\Models\Content\Team;

use App\Models\Concerns\HasConfiguredMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;

class TeamMember extends Model implements HasMedia
{
    use HasConfiguredMedia;

    protected $table = 'content_team_members';

    protected $fillable = [
        'code',
        'is_active',
        'sort_order',
        'email',
        'mobile_phone',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'payload',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'int',
        'payload' => 'array',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(TeamMemberTranslation::class, 'team_member_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(TeamMemberTranslation::class, 'team_member_id')->where('locale', $locale);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
