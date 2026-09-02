<?php

namespace App\Models\Content\Career;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobOpening extends Model
{
    protected $table = 'content_job_openings';

    protected $fillable = [
        'code',
        'is_active',
        'published_at',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'published_at' => 'datetime',
        'sort_order' => 'int',
    ];

    protected static function booted(): void
    {
        static::saving(function (JobOpening $opening): void {
            if ($opening->is_active && $opening->published_at === null) {
                $opening->published_at = now();
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $publishedQuery): void {
                $publishedQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function translations(): HasMany
    {
        return $this->hasMany(JobOpeningTranslation::class, 'job_opening_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(JobOpeningTranslation::class, 'job_opening_id')
            ->where('locale', $locale);
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
