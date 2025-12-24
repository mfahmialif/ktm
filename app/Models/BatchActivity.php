<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchActivity extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_UPLOADED = 'uploaded';

    protected $fillable = [
        'batch_id',
        'action',
        'status',
        'processed_count',
        'failed_count',
        'notes',
        'user_id',
    ];

    /**
     * Generate a unique batch ID.
     */
    public static function generateBatchId(): string
    {
        return '#BATCH-' . date('Y') . '-' . str_pad(static::count() + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who created this batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if batch is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if batch has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get status badge color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_PROCESSING => 'yellow',
            self::STATUS_UPLOADED => 'blue',
            default => 'gray',
        };
    }
}
