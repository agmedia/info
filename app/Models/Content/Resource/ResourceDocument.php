<?php

namespace App\Models\Content\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResourceDocument extends Model
{
    protected $table = 'content_resource_documents';

    protected $fillable = [
        'code',
        'group_code',
        'is_active',
        'published_at',
        'sort_order',
        'download_url',
        'cover_image_url',
        'source_url',
        'payload',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'published_at' => 'datetime',
        'sort_order' => 'int',
        'payload' => 'array',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ResourceDocumentTranslation::class, 'document_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(ResourceDocumentTranslation::class, 'document_id')->where('locale', $locale);
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
