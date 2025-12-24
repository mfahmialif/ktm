<?php

namespace App\Livewire\Admin\KtmGenerator;

use App\Models\AcademicYear;
use App\Models\KtmTemplate;
use App\Models\Student;
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
    public $selectedStudents = [];
    public $selectAll = false;

    // For selecting which template to use when generating
    public $selectedTemplateId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAngkatan' => ['except' => ''],
        'filterProdi' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        // Set default selected template to first active one
        $activeTemplate = KtmTemplate::where('is_active', true)->first();
        if ($activeTemplate) {
            $this->selectedTemplateId = $activeTemplate->id;
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
            ->when($this->filterStatus, function ($query) {
                if ($this->filterStatus === 'ready') {
                    $query->whereNotNull('photo')->whereIn('ktm_status', ['pending', null]);
                } elseif ($this->filterStatus === 'generated') {
                    $query->where('ktm_status', 'generated');
                } elseif ($this->filterStatus === 'no_photo') {
                    $query->whereNull('photo');
                } elseif ($this->filterStatus === 'error') {
                    $query->where('ktm_status', 'error');
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
        return KtmTemplate::where('is_active', true)->orderBy('name')->get();
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
        if ($student->ktm_status === 'error') {
            return 'error';
        }
        if ($student->ktm_status === 'generated') {
            return 'generated';
        }
        if (empty($student->photo)) {
            return 'no_photo';
        }
        return 'ready';
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
        return KtmTemplate::where('is_active', true)->first();
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
            $result = $service->generateForStudent($student);

            if ($result['success']) {
                $student->update([
                    'ktm_status' => 'generated',
                    'ktm_generated_at' => now(),
                    'ktm_file_path' => $result['path'],
                    'ktm_error_message' => null,
                ]);

                $photoInfo = empty($student->photo) ? ' (menggunakan foto default)' : '';
                session()->flash('success', 'KTM untuk ' . $student->name . ' berhasil di-generate' . $photoInfo . '.');
            }
        } catch (\Exception $e) {
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

            $service = $this->getGeneratorService();

            // Allow regenerate for all selected students
            $students = Student::whereIn('id', $this->selectedStudents)
                ->get()
                ->all();

            if (empty($students)) {
                session()->flash('error', 'Tidak ada mahasiswa yang ditemukan.');
                return;
            }

            $results = $service->generateBatch($students);

            $this->selectedStudents = [];
            $this->selectAll = false;

            $msg = $results['success'] . ' KTM berhasil di-generate.';
            if ($results['failed'] > 0) {
                $msg .= ' ' . $results['failed'] . ' gagal.';
            }
            session()->flash('success', $msg);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal generate KTM: ' . $e->getMessage());
        }
    }

    public function testClick()
    {
        dd('LIVEWIRE MASUK');
    }


    public function generateAll()
    {
        try {
            $service = $this->getGeneratorService();

            // Allow regenerate for all students (no ktm_status filter)
            $students = $this->getStudentsQuery()
                ->get()
                ->all();

            if (empty($students)) {
                session()->flash('error', 'Tidak ada mahasiswa yang ditemukan.');
                return;
            }

            $results = $service->generateBatch($students);

            $msg = $results['success'] . ' KTM berhasil di-generate.';
            if ($results['failed'] > 0) {
                $msg .= ' ' . $results['failed'] . ' gagal.';
            }
            session()->flash('success', $msg);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal generate KTM: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.ktm-generator.index', [
            'students' => $this->students,
            'templates' => $this->templates,
            'prodiList' => $this->prodiList,
            'angkatanList' => $this->angkatanList,
        ]);
    }
}
