<?php

namespace App\Livewire\Admin\KtmTemplate;

use App\Models\AcademicYear;
use App\Models\KtmTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAcademicYear = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAcademicYear' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAcademicYear(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = KtmTemplate::query()
            ->with('academicYear')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterAcademicYear, function ($q) {
                $q->where('academic_year_id', $this->filterAcademicYear);
            })
            ->orderBy('created_at', 'desc');

        return view('livewire.admin.ktm-template.index', [
            'templates' => $query->paginate(10),
            'academicYears' => AcademicYear::orderBy('code', 'desc')->get(),
        ]);
    }
}
