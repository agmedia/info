<?php

namespace App\Models\Content\Glossary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GlossaryTerm extends Model
{
    protected $table = 'content_glossary_terms';

    protected $fillable = [
        'code',
        'collection_code',
        'is_active',
        'sort_order',
        'payload',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'int',
        'payload' => 'array',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(GlossaryTermTranslation::class, 'term_id');
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(GlossaryTermTranslation::class, 'term_id')->where('locale', $locale);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
