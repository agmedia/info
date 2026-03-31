<?php

namespace App\Models\Content\Call;

use App\Models\Concerns\HasConfiguredMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;

class CallPost extends Model implements HasMedia
{
    use HasConfiguredMedia;

    protected $table = 'content_call_posts';

    protected $fillable = [
        'code',
        'is_active',
        'is_featured',
        'published_at',
        'sort_order',
        'payload',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_featured' => 'bool',
        'published_at' => 'datetime',
        'sort_order' => 'int',
        'payload' => 'array',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CallPostTranslation::class, 'post_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(CallPostTranslation::class, 'post_id')->where('locale', $locale);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Catalog\Category\Category::class,
            'content_call_post_category',
            'post_id',
            'category_id'
        )
            ->withPivot(['sort_order', 'is_primary'])
            ->withTimestamps();
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
