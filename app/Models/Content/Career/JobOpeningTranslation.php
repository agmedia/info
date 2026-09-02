<?php

namespace App\Models\Content\Career;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOpeningTranslation extends Model
{
    protected $table = 'content_job_opening_translations';

    protected $fillable = [
        'job_opening_id',
        'locale',
        'title',
        'slug',
        'locations',
        'excerpt',
        'body_html',
        'meta_title',
        'meta_description',
    ];

    public function opening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }
}
