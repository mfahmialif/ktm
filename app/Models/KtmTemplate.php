<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KtmTemplate extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'academic_year_id',
        'name',
        'front_template',
        'back_template',
        'is_active',
        'status',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the academic year for this template.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all student KTM statuses for this template.
     */
    public function studentKtmStatuses(): HasMany
    {
        return $this->hasMany(StudentKtmStatus::class);
    }

    /**
     * Scope for active template.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the currently active template.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    /**
     * Check if template is configured (has front template).
     */
    public function isConfigured(): bool
    {
        return !empty($this->front_template);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_INCOMPLETE => 'yellow',
            self::STATUS_ARCHIVED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INCOMPLETE => 'Incomplete',
            self::STATUS_ARCHIVED => 'Archived',
            default => ucfirst($this->status ?? 'unknown'),
        };
    }
}
