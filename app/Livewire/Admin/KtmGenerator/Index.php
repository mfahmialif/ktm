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

    // For selecting which academic year template to use when generating
    public $generateAcademicYear = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAngkatan' => ['except' => ''],
        'filterProdi' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        $activeYear = AcademicYear::active()->first();
        if ($activeYear) {
            $this->generateAcademicYear = $activeYear->id;
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
                $query->where('angkatan', $this->filterAngkatan);
            })
            ->orderBy('name');
    }

    public function getStudentsProperty()
    {
        return $this->getStudentsQuery()->paginate(10);
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::orderBy('code', 'desc')->get();
    }

    public function getProdiListProperty()
    {
        return Student::whereNotNull('prodi')
            ->distinct()
            ->pluck('prodi')
            ->sort()
            ->values();
    }

    public function getAngkatanListProperty()
    {
        return Student::whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->sortDesc()
            ->values();
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
     * Get the active template for the selected academic year
     */
    protected function getActiveTemplate(): ?KtmTemplate
    {
        // First try to get template linked to academic year
        $academicYear = AcademicYear::find($this->generateAcademicYear);

        // Get any active template for now (can be enhanced to link template to academic year)
        return KtmTemplate::where('is_active', true)->first();
    }

    /**
     * Get KTM Generator Service instance
     */
    protected function getGeneratorService(): KtmGeneratorService
    {
        $service = new KtmGeneratorService();

        $template = $this->getActiveTemplate();
        if (!$template) {
            throw new \Exception('Tidak ada template KTM yang aktif. Silakan aktifkan template terlebih dahulu.');
        }

        $service->setTemplate($template);

        $academicYear = AcademicYear::find($this->generateAcademicYear);
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
            $service = $this->getGeneratorService();

            $students = Student::whereIn('id', $this->selectedStudents)
                ->where('ktm_status', '!=', 'generated')
                ->get()
                ->all();

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

    public function generateAll()
    {
        try {
            $service = $this->getGeneratorService();

            $students = $this->getStudentsQuery()
                ->where('ktm_status', '!=', 'generated')
                ->get()
                ->all();

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
            'academicYears' => $this->academicYears,
            'prodiList' => $this->prodiList,
            'angkatanList' => $this->angkatanList,
        ]);
    }
}
