<?php

namespace App\Livewire\Admin\AcademicYear;

use App\Models\AcademicYear;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterSemester = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSemester' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSemester(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AcademicYear::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterSemester, function ($q) {
                $q->where('semester', $this->filterSemester);
            })
            ->orderBy('code', 'desc');

        return view('livewire.admin.academic-year.index', [
            'academicYears' => $query->paginate(10),
        ]);
    }
}
