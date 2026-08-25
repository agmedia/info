<?php

namespace App\Models\Content\Service;

use App\Support\Content\ServicePageTemplateRegistry;
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

    protected static function booted(): void
    {
        static::saving(function (self $translation): void {
            $templateKey = $translation->relationLoaded('servicePage')
                ? (string) $translation->servicePage?->template_key
                : (string) ServicePage::query()
                    ->whereKey($translation->service_page_id)
                    ->value('template_key');
            $canonicalSlug = ServicePageTemplateRegistry::canonicalStructuralSlug(
                $templateKey,
                (string) $translation->locale
            );

            if ($canonicalSlug !== null) {
                $translation->slug = $canonicalSlug;
            }
        });
    }

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class, 'service_page_id');
    }
}
