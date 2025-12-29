<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoDownloadJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'total_students',
        'processed_students',
        'success_count',
        'failed_count',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Generate a new unique batch ID.
     */
    public static function generateBatchId(): string
    {
        return 'PHOTO-' . Str::uuid()->toString();
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_students === 0) {
            return 0;
        }
        return (int) round(($this->processed_students / $this->total_students) * 100);
    }

    /**
     * Check if batch is still running.
     */
    public function isRunning(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if batch is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Increment processed count.
     */
    public function incrementProcessed(bool $success = true): void
    {
        // Use database increment to avoid race conditions
        if ($success) {
            self::where('id', $this->id)->update([
                'processed_students' => DB::raw('processed_students + 1'),
                'success_count' => DB::raw('success_count + 1'),
            ]);
        } else {
            self::where('id', $this->id)->update([
                'processed_students' => DB::raw('processed_students + 1'),
                'failed_count' => DB::raw('failed_count + 1'),
            ]);
        }

        // Refresh to get updated values
        $this->refresh();

        // Check if all done
        if ($this->processed_students >= $this->total_students) {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Mark as processing.
     */
    public function markAsProcessing(): void
    {
        if ($this->status === 'pending') {
            $this->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);
        }
    }

    /**
     * Get the latest active batch.
     */
    public static function getActiveBatch(): ?self
    {
        return static::whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at')
            ->first();
    }
}
