<?php

namespace App\Livewire\Admin\DownloadJobs;

use App\Models\KtmDownloadJob;
use App\Models\KtmTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterTemplate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTemplate' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTemplate()
    {
        $this->resetPage();
    }

    public function getDownloadJobsProperty()
    {
        $query = KtmDownloadJob::with(['template', 'user'])
            ->orderByDesc('created_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('download_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('template', function ($templateQuery) {
                        $templateQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterTemplate) {
            $query->where('ktm_template_id', $this->filterTemplate);
        }

        return $query->paginate(20);
    }

    public function getAllTemplatesProperty()
    {
        return KtmTemplate::orderBy('name')->get();
    }

    public function deleteJob($id)
    {
        $downloadJob = KtmDownloadJob::findOrFail($id);

        // Delete ZIP file if exists
        if ($downloadJob->zip_path && \Storage::disk('public')->exists($downloadJob->zip_path)) {
            \Storage::disk('public')->delete($downloadJob->zip_path);
        }

        $downloadJob->delete();

        session()->flash('success', 'Download job berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.admin.download-jobs.index', [
            'downloadJobs' => $this->downloadJobs,
            'allTemplates' => $this->allTemplates,
        ])->layout('layouts.app');
    }
}
