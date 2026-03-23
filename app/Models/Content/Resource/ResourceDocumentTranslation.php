<?php

namespace App\Models\Content\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceDocumentTranslation extends Model
{
    protected $table = 'content_resource_document_translations';

    protected $fillable = [
        'document_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'meta_title',
        'meta_description',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ResourceDocument::class, 'document_id');
    }
}
