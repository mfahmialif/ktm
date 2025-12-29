<?php

namespace App\Jobs;

use App\Models\PhotoDownloadJob;
use App\Models\Student;
use App\Services\PMBService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadPhotoBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 600; // 10 minutes per batch

    public array $studentIds;
    public string $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $studentIds, string $batchId)
    {
        $this->studentIds = $studentIds;
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get batch job for progress tracking
        $batchJob = PhotoDownloadJob::where('batch_id', $this->batchId)->first();
        if ($batchJob) {
            $batchJob->markAsProcessing();
            Log::info("DownloadPhotoBatchJob: Starting batch {$this->batchId} with " . count($this->studentIds) . " students");
        } else {
            Log::warning("DownloadPhotoBatchJob: Batch job not found for ID {$this->batchId}");
            return;
        }

        $students = Student::whereIn('id', $this->studentIds)->get();

        foreach ($students as $student) {
            $success = false;

            try {
                // Get NIK from student record
                $nik = $student->nik;
                
                if (empty($nik)) {
                    Log::warning("DownloadPhotoBatchJob: Student {$student->id} has no NIK");
                    $this->updateProgress($batchJob, false);
                    continue;
                }

                // Call PMB API to get photo URL
                $response = PMBService::foto($nik, 'S1');

                if (!$response || !isset($response->status) || !$response->status) {
                    Log::warning("DownloadPhotoBatchJob: Failed to get photo for student {$student->id}, NIK: {$nik}");
                    $this->updateProgress($batchJob, false);
                    continue;
                }

                $photoUrl = $response->data ?? null;

                if (empty($photoUrl)) {
                    Log::warning("DownloadPhotoBatchJob: No photo URL returned for student {$student->id}");
                    $this->updateProgress($batchJob, false);
                    continue;
                }

                // Download the photo with extended timeout (120 seconds for large files)
                $photoContent = Http::timeout(120)->get($photoUrl)->body();

                if (empty($photoContent)) {
                    Log::warning("DownloadPhotoBatchJob: Failed to download photo for student {$student->id}");
                    $this->updateProgress($batchJob, false);
                    continue;
                }

                // Determine file extension from URL or default to jpg
                $extension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                
                // Generate filename using NIM
                $filename = "photos/{$student->nim}.{$extension}";

                // Save photo to storage
                Storage::disk('public')->put($filename, $photoContent);

                // Update student's photo field with the path
                $student->update(['photo' => $filename]);

                Log::info("DownloadPhotoBatchJob: Successfully downloaded photo for student {$student->id}");
                $success = true;

            } catch (\Exception $e) {
                Log::error("DownloadPhotoBatchJob: Failed to download photo for student {$student->id}: " . $e->getMessage());
            }

            $this->updateProgress($batchJob, $success);
        }

        Log::info("DownloadPhotoBatchJob: Completed batch of " . count($this->studentIds) . " students");
    }

    /**
     * Update progress - reload batch job from DB to avoid stale data
     */
    protected function updateProgress(PhotoDownloadJob $batchJob, bool $success): void
    {
        $freshBatchJob = PhotoDownloadJob::where('batch_id', $this->batchId)->first();
        if ($freshBatchJob) {
            $freshBatchJob->incrementProcessed($success);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("DownloadPhotoBatchJob failed: " . $exception->getMessage());

        // Mark batch as failed if exists
        $batchJob = PhotoDownloadJob::where('batch_id', $this->batchId)->first();
        if ($batchJob) {
            $batchJob->update(['status' => 'failed']);
        }
    }
}
