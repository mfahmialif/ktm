<?php

namespace App\Jobs;

use App\Models\KtmDownloadJob;
use App\Models\KtmTemplate;
use App\Models\Student;
use App\Models\StudentKtmStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class CreateKtmZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600; // 10 minutes

    public array $studentIds;
    public int $templateId;
    public string $downloadId;

    public function __construct(array $studentIds, int $templateId, string $downloadId)
    {
        $this->studentIds = $studentIds;
        $this->templateId = $templateId;
        $this->downloadId = $downloadId;
    }

    public function handle(): void
    {
        $template = KtmTemplate::find($this->templateId);
        if (!$template) {
            Log::error("CreateKtmZipJob: Template {$this->templateId} not found");
            return;
        }

        // Get download job
        $downloadJob = KtmDownloadJob::where('download_id', $this->downloadId)->first();
        if (!$downloadJob) {
            Log::warning("CreateKtmZipJob: Download job not found for ID {$this->downloadId}");
            return;
        }

        $downloadJob->markAsProcessing();
        Log::info("CreateKtmZipJob: Starting download {$this->downloadId} with " . count($this->studentIds) . " files");

        try {
            // Create ZIP file
            $templateSlug = Str::slug($template->name);
            $zipFilename = "{$this->downloadId}.zip";
            $zipDir = "downloads/{$templateSlug}";
            $zipPath = "{$zipDir}/{$zipFilename}";

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($zipDir);

            $fullZipPath = Storage::disk('public')->path($zipPath);

            $zip = new ZipArchive();
            if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Failed to create ZIP file");
            }

            // Get student KTM files
            $ktmStatuses = StudentKtmStatus::whereIn('student_id', $this->studentIds)
                ->where('ktm_template_id', $template->id)
                ->where('status', 'generated')
                ->whereNotNull('file_path')
                ->with('student')
                ->get();

            Log::info("CreateKtmZipJob: Found {$ktmStatuses->count()} KTM files to add to ZIP");

            if ($ktmStatuses->count() === 0) {
                throw new \Exception("No generated KTM files found for the selected students");
            }

            $processedCount = 0;
            foreach ($ktmStatuses as $status) {
                if (!$status->file_path) {
                    Log::warning("CreateKtmZipJob: Student {$status->student_id} has no file_path");
                    continue;
                }

                $fullPath = Storage::disk('public')->path($status->file_path);

                if (!file_exists($fullPath)) {
                    Log::warning("CreateKtmZipJob: File not found at {$fullPath}");
                    continue;
                }

                $student = $status->student;
                $filename = "{$student->nim}_{$student->name}.png";

                // Sanitize filename
                $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);

                // Add file to ZIP
                $added = $zip->addFile($fullPath, $filename);

                if (!$added) {
                    Log::error("CreateKtmZipJob: Failed to add file {$fullPath} to ZIP");
                    continue;
                }

                $processedCount++;
                Log::info("CreateKtmZipJob: Added file #{$processedCount}: {$filename}");

                // Update progress every 10 files
                if ($processedCount % 10 === 0) {
                    $downloadJob->update(['processed_files' => $processedCount]);
                }
            }

            if ($processedCount === 0) {
                throw new \Exception("No KTM files could be added to ZIP - all files may be missing from storage");
            }

            Log::info("CreateKtmZipJob: Added {$processedCount} files to ZIP, closing...");
            $zip->close();

            // Get ZIP file size using absolute path
            if (!file_exists($fullZipPath)) {
                throw new \Exception("ZIP file was not created successfully");
            }

            $zipSize = filesize($fullZipPath);

            // Update final progress
            $downloadJob->update(['processed_files' => $processedCount]);
            $downloadJob->markAsCompleted($zipPath, $zipSize);

            Log::info("CreateKtmZipJob: Completed download {$this->downloadId} - {$processedCount} files, " . ($zipSize / 1024 / 1024) . " MB");
        } catch (\Exception $e) {
            Log::error("CreateKtmZipJob: Failed - " . $e->getMessage());
            $downloadJob->markAsFailed($e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("CreateKtmZipJob failed: " . $exception->getMessage());

        $downloadJob = KtmDownloadJob::where('download_id', $this->downloadId)->first();
        if ($downloadJob) {
            $downloadJob->markAsFailed($exception->getMessage());
        }
    }
}
