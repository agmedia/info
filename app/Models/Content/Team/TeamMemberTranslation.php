<?php

namespace App\Models\Content\Team;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMemberTranslation extends Model
{
    protected $table = 'content_team_member_translations';

    protected $fillable = [
        'team_member_id',
        'locale',
        'name',
        'position',
        'departments',
        'description_html',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }
}
