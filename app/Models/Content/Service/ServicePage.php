<?php

namespace App\Models\Content\Service;

use App\Models\Concerns\HasConfiguredMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;

class ServicePage extends Model implements HasMedia
{
    use HasConfiguredMedia;

    protected $table = 'content_service_pages';

    protected $fillable = [
        'code',
        'template_key',
        'is_active',
        'published_at',
        'sort_order',
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
        return $this->hasMany(ServicePageTranslation::class, 'service_page_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(ServicePageTranslation::class, 'service_page_id')->where('locale', $locale);
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
