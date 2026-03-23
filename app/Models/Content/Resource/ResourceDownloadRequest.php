<?php

namespace App\Models\Content\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceDownloadRequest extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'user_id',
        'document_id',
        'document_code',
        'document_title',
        'document_slug',
        'document_group_code',
        'document_download_url',
        'name',
        'email',
        'phone',
        'company',
        'status',
        'locale',
        'ip_address',
        'user_agent',
        'payload',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'document_id' => 'int',
        'payload' => 'array',
        'reviewed_by' => 'int',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ResourceDocument::class, 'document_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}
