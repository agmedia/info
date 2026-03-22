<?php

namespace App\Models\Content\Glossary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlossaryTermTranslation extends Model
{
    protected $table = 'content_glossary_term_translations';

    protected $fillable = [
        'term_id',
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

    public function term(): BelongsTo
    {
        return $this->belongsTo(GlossaryTerm::class, 'term_id');
    }
}
