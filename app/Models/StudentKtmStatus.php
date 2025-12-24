<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentKtmStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'ktm_template_id',
        'status',
        'file_path',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    /**
     * Get the student that owns this status.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the template associated with this status.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(KtmTemplate::class, 'ktm_template_id');
    }

    /**
     * Check if the KTM has been generated.
     */
    public function isGenerated(): bool
    {
        return $this->status === 'generated' && !empty($this->file_path);
    }

    /**
     * Scope for generated status.
     */
    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    /**
     * Scope for pending status.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for error status.
     */
    public function scopeError($query)
    {
        return $query->where('status', 'error');
    }
}
