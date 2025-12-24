<?php

namespace App\Livewire\Admin\KtmGenerator;

use App\Jobs\GenerateKtmBatchJob;
use App\Models\AcademicYear;
use App\Models\KtmBatchJob;
use App\Models\KtmTemplate;
use App\Models\Student;
use App\Models\StudentKtmStatus;
use App\Services\KtmGeneratorService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterAngkatan = '';
    public $filterProdi = '';
    public $filterStatus = '';
    public $filterTemplate = ''; // New filter by template status
    public $selectedStudents = [];
    public $selectAll = false;

    // For selecting which template to use when generating
    public $selectedTemplateId = '';

    // For batch progress tracking
    public $activeBatchId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAngkatan' => ['except' => ''],
        'filterProdi' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTemplate' => ['except' => ''],
    ];

    public function mount()
    {
        // Set default selected template to first active one
        $activeTemplate = KtmTemplate::latest()->first();
        if ($activeTemplate) {
            $this->selectedTemplateId = $activeTemplate->id;
            $this->filterTemplate = $activeTemplate->id; // Also set as filter
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterProdi()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterAngkatan()
    {
        $this->resetPage();
    }

    public function updatingFilterTemplate()
    {
        $this->resetPage();
    }

    public function updatedFilterTemplate($value)
    {
        // Sync selected template with filter template
        if ($value) {
            $this->selectedTemplateId = $value;
        }
    }

    public function updatedSelectedTemplateId($value)
    {
        // Sync filter template with selected template
        if ($value) {
            $this->filterTemplate = $value;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function getStudentsQuery()
    {
        $templateId = $this->filterTemplate ?: $this->selectedTemplateId;

        return Student::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nim', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('prodi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterProdi, function ($query) {
                $query->where('prodi', $this->filterProdi);
            })
            ->when($this->filterStatus && $templateId, function ($query) use ($templateId) {
                if ($this->filterStatus === 'ready') {
                    // Students with photo and no generated status for this template
                    $query->whereNotNull('photo')
                        ->where('photo', '!=', '')
                        ->whereDoesntHave('ktmStatuses', function ($q) use ($templateId) {
                            $q->where('ktm_template_id', $templateId)
                                ->where('status', 'generated');
                        });
                } elseif ($this->filterStatus === 'generated') {
                    // Students with generated status for this template
                    $query->whereHas('ktmStatuses', function ($q) use ($templateId) {
                        $q->where('ktm_template_id', $templateId)
                            ->where('status', 'generated');
                    });
                } elseif ($this->filterStatus === 'no_photo') {
                    $query->where(function ($q) {
                        $q->whereNull('photo')->orWhere('photo', '');
                    });
                } elseif ($this->filterStatus === 'error') {
                    // Students with error status for this template
                    $query->whereHas('ktmStatuses', function ($q) use ($templateId) {
                        $q->where('ktm_template_id', $templateId)
                            ->where('status', 'error');
                    });
                }
            })
            ->when($this->filterAngkatan, function ($query) {
                // Filter by first 4 digits of NIM (angkatan year)
                $query->whereRaw('SUBSTRING(nim, 1, 4) = ?', [$this->filterAngkatan]);
            })
            ->orderBy('name');
    }

    public function getStudentsProperty()
    {
        return $this->getStudentsQuery()->paginate(10);
    }

    public function getTemplatesProperty()
    {
        return KtmTemplate::orderBy('name')->get();
    }

    public function getAllTemplatesProperty()
    {
        return KtmTemplate::orderBy('name')->get();
    }

    public function getProdiListProperty()
    {
        return Student::whereNotNull('prodi')
            ->where('prodi', '!=', '')
            ->select('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');
    }

    public function getAngkatanListProperty()
    {
        // Extract first 4 digits from NIM as angkatan year
        return Student::whereNotNull('nim')
            ->where('nim', '!=', '')
            ->selectRaw('DISTINCT SUBSTRING(nim, 1, 4) as angkatan')
            ->orderByDesc('angkatan')
            ->pluck('angkatan')
            ->filter(fn($val) => is_numeric($val) && strlen($val) == 4);
    }

    public function getStudentStatus($student)
    {
        $templateId = $this->filterTemplate ?: $this->selectedTemplateId;
        return $student->getStatusForTemplate($templateId ? (int) $templateId : null);
    }

    public function getStudentKtmStatus($student)
    {
        $templateId = $this->filterTemplate ?: $this->selectedTemplateId;
        if (!$templateId) {
            return null;
        }
        return $student->getKtmStatusForTemplate((int) $templateId);
    }

    /**
     * Get the selected template
     */
    protected function getSelectedTemplate(): ?KtmTemplate
    {
        if ($this->selectedTemplateId) {
            return KtmTemplate::find($this->selectedTemplateId);
        }
        // Fallback to first active template
        return KtmTemplate::latest()->first();
    }

    /**
     * Get KTM Generator Service instance
     */
    protected function getGeneratorService(): KtmGeneratorService
    {
        $service = new KtmGeneratorService();

        $template = $this->getSelectedTemplate();
        if (!$template) {
            throw new \Exception('Silakan pilih template terlebih dahulu.');
        }

        $service->setTemplate($template);

        // Use active academic year for output folder
        $academicYear = AcademicYear::active()->first();
        if ($academicYear) {
            $service->setAcademicYear($academicYear);
        }

        return $service;
    }

    public function generateSingle($studentId)
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                session()->flash('error', 'Student tidak ditemukan.');
                return;
            }

            $service = $this->getGeneratorService();
            $template = $this->getSelectedTemplate();

            if (!$template) {
                session()->flash('error', 'Silakan pilih template terlebih dahulu.');
                return;
            }

            $result = $service->generateForStudent($student);

            if ($result['success']) {
                // Save status to pivot table
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

                $photoInfo = empty($student->photo) ? ' (menggunakan foto default)' : '';
                session()->flash('success', 'KTM untuk ' . $student->name . ' berhasil di-generate' . $photoInfo . '.');
            }
        } catch (\Exception $e) {
            // Save error status to pivot table
            $template = $this->getSelectedTemplate();
            if ($template && isset($student)) {
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
            session()->flash('error', 'Gagal generate KTM: ' . $e->getMessage());
        }
    }

    public function generateBulk()
    {
        try {
            if (empty($this->selectedStudents)) {
                session()->flash('error', 'Tidak ada mahasiswa yang dipilih.');
                return;
            }

            $template = $this->getSelectedTemplate();
            if (!$template) {
                session()->flash('error', 'Silakan pilih template terlebih dahulu.');
                return;
            }

            // Get student IDs
            $studentIds = Student::whereIn('id', $this->selectedStudents)
                ->pluck('id')
                ->toArray();

            if (empty($studentIds)) {
                session()->flash('error', 'Tidak ada mahasiswa yang ditemukan.');
                return;
            }

            // Create batch job record for progress tracking
            $batchId = KtmBatchJob::generateBatchId();
            $batchJob = KtmBatchJob::create([
                'ktm_template_id' => $template->id,
                'batch_id' => $batchId,
                'total_students' => count($studentIds),
                'status' => 'pending',
            ]);

            $this->activeBatchId = $batchId;

            // Dispatch jobs in chunks of 50
            $chunkSize = 50;
            $chunks = array_chunk($studentIds, $chunkSize);

            foreach ($chunks as $chunk) {
                GenerateKtmBatchJob::dispatch($chunk, $template->id, $batchId);
            }

            $this->selectedStudents = [];
            $this->selectAll = false;

            $totalStudents = count($studentIds);
            session()->flash('success', "Proses generate {$totalStudents} KTM telah dimulai. Lihat progress di bawah.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menjadwalkan generate KTM: ' . $e->getMessage());
        }
    }

    public function testClick()
    {
        dd('LIVEWIRE MASUK');
    }


    public function generateAll()
    {
        try {
            $template = $this->getSelectedTemplate();
            if (!$template) {
                session()->flash('error', 'Silakan pilih template terlebih dahulu.');
                return;
            }

            // Get all student IDs based on current filters
            $studentIds = $this->getStudentsQuery()
                ->pluck('id')
                ->toArray();

            if (empty($studentIds)) {
                session()->flash('error', 'Tidak ada mahasiswa yang ditemukan.');
                return;
            }

            // Create batch job record for progress tracking
            $batchId = KtmBatchJob::generateBatchId();
            $batchJob = KtmBatchJob::create([
                'ktm_template_id' => $template->id,
                'batch_id' => $batchId,
                'total_students' => count($studentIds),
                'status' => 'pending',
            ]);

            $this->activeBatchId = $batchId;

            // Dispatch jobs in chunks of 50
            $chunkSize = 50;
            $chunks = array_chunk($studentIds, $chunkSize);

            foreach ($chunks as $chunk) {
                GenerateKtmBatchJob::dispatch($chunk, $template->id, $batchId);
            }

            $totalStudents = count($studentIds);
            session()->flash('success', "Proses generate {$totalStudents} KTM telah dimulai. Lihat progress di bawah.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menjadwalkan generate KTM: ' . $e->getMessage());
        }
    }

    public function getActiveBatchProperty()
    {
        if ($this->activeBatchId) {
            return KtmBatchJob::where('batch_id', $this->activeBatchId)->first();
        }

        // Check for any active batch for current template
        $templateId = $this->filterTemplate ?: $this->selectedTemplateId;
        if ($templateId) {
            return KtmBatchJob::getActiveBatch((int) $templateId);
        }

        return null;
    }

    public function dismissBatch()
    {
        $this->activeBatchId = null;
    }

    public function render()
    {
        return view('livewire.admin.ktm-generator.index', [
            'students' => $this->students,
            'templates' => $this->templates,
            'allTemplates' => $this->allTemplates,
            'prodiList' => $this->prodiList,
            'angkatanList' => $this->angkatanList,
            'activeBatch' => $this->activeBatch,
        ]);
    }
}
