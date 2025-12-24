<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the KTM templates for this academic year.
     */
    public function ktmTemplates(): HasMany
    {
        return $this->hasMany(KtmTemplate::class);
    }

    /**
     * Scope for active academic year.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the currently active academic year.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    /**
     * Set this academic year as active (deactivating others).
     */
    public function setAsActive(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * Get semester badge color.
     */
    public function getSemesterColorAttribute(): string
    {
        return $this->semester === 'ganjil' ? 'blue' : 'purple';
    }
}
