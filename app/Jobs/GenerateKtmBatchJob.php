<?php

namespace App\Jobs;

use App\Models\KtmBatchJob;
use App\Models\KtmTemplate;
use App\Models\Student;
use App\Models\StudentKtmStatus;
use App\Services\KtmGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateKtmBatchJob implements ShouldQueue
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
    public int $templateId;
    public string $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $studentIds, int $templateId, string $batchId)
    {
        $this->studentIds = $studentIds;
        $this->templateId = $templateId;
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $template = KtmTemplate::find($this->templateId);
        if (!$template) {
            Log::error("GenerateKtmBatchJob: Template {$this->templateId} not found");
            return;
        }

        // Get batch job for progress tracking
        $batchJob = KtmBatchJob::where('batch_id', $this->batchId)->first();
        if ($batchJob) {
            $batchJob->markAsProcessing();
            Log::info("GenerateKtmBatchJob: Starting batch {$this->batchId} with " . count($this->studentIds) . " students");
        } else {
            Log::warning("GenerateKtmBatchJob: Batch job not found for ID {$this->batchId}");
        }

        $service = new KtmGeneratorService();
        $service->setTemplate($template);

        $students = Student::whereIn('id', $this->studentIds)->get();

        foreach ($students as $student) {
            $success = false;

            try {
                $result = $service->generateForStudent($student);

                if ($result['success']) {
                    StudentKtmStatus::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'ktm_template_id' => $template->id,
                        ],
                        [
                            'status' => 'generated',
                            'file_path' => $result['path'],
                            'error_message' => null,
                            'generated_at' => now(),
                        ]
                    );
                    $success = true;
                }
            } catch (\Exception $e) {
                Log::error("GenerateKtmBatchJob: Failed to generate KTM for student {$student->id}: " . $e->getMessage());

                StudentKtmStatus::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'ktm_template_id' => $template->id,
                    ],
                    [
                        'status' => 'error',
                        'error_message' => $e->getMessage(),
                    ]
                );
            }

            // Update progress - reload batch job from DB to avoid stale data
            if ($this->batchId) {
                $freshBatchJob = KtmBatchJob::where('batch_id', $this->batchId)->first();
                if ($freshBatchJob) {
                    $freshBatchJob->incrementProcessed($success);
                }
            }
        }

        Log::info("GenerateKtmBatchJob: Completed batch of " . count($this->studentIds) . " students for template {$template->name}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateKtmBatchJob failed: " . $exception->getMessage());

        // Mark batch as failed if exists
        $batchJob = KtmBatchJob::where('batch_id', $this->batchId)->first();
        if ($batchJob) {
            $batchJob->update(['status' => 'failed']);
        }
    }
}
