<?php

namespace App\Models\Content\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApplication extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_RESOLVED = 'resolved';
    public const CV_DISK = 'local';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'message',
        'cv_path',
        'cv_disk',
        'cv_original_name',
        'cv_mime_type',
        'cv_size',
        'status',
        'ip_address',
        'user_agent',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'cv_size' => 'int',
        'reviewed_by' => 'int',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            trim((string) $this->first_name),
            trim((string) $this->last_name),
        ])));
    }

    public function getCvSizeHumanAttribute(): string
    {
        $bytes = (int) ($this->cv_size ?? 0);
        if ($bytes <= 0) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $value = (float) $bytes;
        $unitIndex = 0;

        while ($value >= 1024 && $unitIndex < count($units) - 1) {
            $value /= 1024;
            $unitIndex++;
        }

        $precision = $value >= 10 || $unitIndex === 0 ? 0 : 1;

        return number_format($value, $precision, '.', '').' '.$units[$unitIndex];
    }

    public function downloadName(): string
    {
        return trim((string) $this->cv_original_name) !== ''
            ? (string) $this->cv_original_name
            : basename((string) $this->cv_path);
    }
}
