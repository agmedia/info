<?php

namespace App\Models\Content\Call;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallPostTranslation extends Model
{
    protected $table = 'content_call_post_translations';

    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'body_html',
        'meta_title',
        'meta_description',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CallPost::class, 'post_id');
    }
}
