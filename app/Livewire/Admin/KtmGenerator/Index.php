<?php

namespace App\Livewire\Admin\KtmGenerator;

use App\Models\AcademicYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterAngkatan = '';  // For filtering students by angkatan
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
        // Set default generate academic year to active one
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
                    $query->whereNotNull('photo')->where('ktm_status', 'pending');
                } elseif ($this->filterStatus === 'generated') {
                    $query->where('ktm_status', 'generated');
                } elseif ($this->filterStatus === 'missing_photo') {
                    $query->whereNull('photo');
                }
            })
            ->when($this->filterAngkatan, function ($query) {
                // Filter by angkatan directly
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
        if (empty($student->photo)) {
            return 'missing_photo';
        }
        if ($student->ktm_status === 'generated') {
            return 'generated';
        }
        return 'ready';
    }

    public function generateSingle($studentId)
    {
        // TODO: Implement actual KTM generation
        $student = Student::find($studentId);
        if ($student && !empty($student->photo)) {
            $student->update([
                'ktm_status' => 'generated',
                'ktm_generated_at' => now(),
            ]);
            session()->flash('success', 'KTM untuk ' . $student->name . ' berhasil di-generate.');
        }
    }

    public function generateBulk()
    {
        $count = 0;
        $students = Student::whereIn('id', $this->selectedStudents)
            ->whereNotNull('photo')
            ->where('ktm_status', '!=', 'generated')
            ->get();

        foreach ($students as $student) {
            // TODO: Implement actual KTM generation
            $student->update([
                'ktm_status' => 'generated',
                'ktm_generated_at' => now(),
            ]);
            $count++;
        }

        $this->selectedStudents = [];
        $this->selectAll = false;

        session()->flash('success', $count . ' KTM berhasil di-generate.');
    }

    public function generateAll()
    {
        $count = 0;
        $students = $this->getStudentsQuery()
            ->whereNotNull('photo')
            ->where('ktm_status', '!=', 'generated')
            ->get();

        foreach ($students as $student) {
            // TODO: Implement actual KTM generation
            $student->update([
                'ktm_status' => 'generated',
                'ktm_generated_at' => now(),
            ]);
            $count++;
        }

        session()->flash('success', $count . ' KTM berhasil di-generate.');
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
