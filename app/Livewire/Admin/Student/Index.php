<?php

namespace App\Livewire\Admin\Student;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProdi = '';
    public $filterAngkatan = '';
    public $filterJenisKelamin = '';
    public $perPage = 10;

    public function updatedFilterJenisKelamin()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterProdi()
    {
        $this->resetPage();
    }

    public function updatedFilterAngkatan()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $student = Student::findOrFail($id);

        // Delete photo if exists
        if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        session()->flash('success', 'Student deleted successfully.');
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

    public function render()
    {
        $query = Student::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterProdi) {
            $query->where('prodi', $this->filterProdi);
        }

        if ($this->filterAngkatan) {
            $query->whereRaw('SUBSTRING(nim, 1, 4) = ?', [$this->filterAngkatan]);
        }

        if ($this->filterJenisKelamin) {
            $query->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        // Get lists for filters
        $prodiList = Student::distinct()->whereNotNull('prodi')->pluck('prodi');

        return view('livewire.admin.student.index', [
            'students' => $students,
            'prodiList' => $prodiList,
            'angkatanList' => $this->angkatanList,
        ]);
    }
}
