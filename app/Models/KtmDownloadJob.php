<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KtmDownloadJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'ktm_template_id',
        'user_id',
        'download_id',
        'filter_criteria',
        'total_files',
        'processed_files',
        'status',
        'zip_path',
        'zip_size',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'filter_criteria' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the template for this download.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(KtmTemplate::class, 'ktm_template_id');
    }

    /**
     * Get the user who created this download.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new unique download ID.
     */
    public static function generateDownloadId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_files === 0) {
            return 0;
        }
        return (int) round(($this->processed_files / $this->total_files) * 100);
    }

    /**
     * Check if download is still running.
     */
    public function isRunning(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if download is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Scope for completed downloads.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending downloads.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->zip_size) {
            return '-';
        }

        $bytes = $this->zip_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
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
     * Mark as completed.
     */
    public function markAsCompleted(string $zipPath, int $zipSize): void
    {
        $this->update([
            'status' => 'completed',
            'zip_path' => $zipPath,
            'zip_size' => $zipSize,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Get the latest active download for a template.
     */
    public static function getActiveDownload(?int $templateId = null): ?self
    {
        $query = static::whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at');

        if ($templateId) {
            $query->where('ktm_template_id', $templateId);
        }

        return $query->first();
    }
}
