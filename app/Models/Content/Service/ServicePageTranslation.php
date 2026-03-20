<?php

namespace App\Models\Content\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageTranslation extends Model
{
    protected $table = 'content_service_page_translations';

    protected $fillable = [
        'service_page_id',
        'locale',
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class, 'service_page_id');
    }
}
