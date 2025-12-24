<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KtmTemplateController;
use App\Http\Controllers\ProfileController;
use App\Models\KtmTemplate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Students
    Route::get('students/export', [\App\Http\Controllers\StudentController::class, 'export'])->name('students.export');
    Route::get('students/template', [\App\Http\Controllers\StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::get('students/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('students.import');
    Route::post('students/import', [\App\Http\Controllers\StudentController::class, 'importStore'])->name('students.import.store');
    Route::resource('students', \App\Http\Controllers\StudentController::class);

    // Academic Years (Tahun Akademik)
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    Route::patch('/academic-years/{academicYear}/toggle-active', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle-active');

    // KTM Templates
    Route::get('/templates', [KtmTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [KtmTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [KtmTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{ktmTemplate}/edit', [KtmTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{ktmTemplate}', [KtmTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{ktmTemplate}', [KtmTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::get('/templates/{ktmTemplate}/upload', [KtmTemplateController::class, 'showUploadForm'])->name('templates.upload');
    Route::post('/templates/{ktmTemplate}/upload', [KtmTemplateController::class, 'upload'])->name('templates.upload.store');
    Route::patch('/templates/{ktmTemplate}/toggle-status', [KtmTemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

    // Template Configuration
    Route::get('/templates/{ktmTemplate}/configure', [KtmTemplateController::class, 'configure'])->name('templates.configure');
    Route::post('/templates/{ktmTemplate}/configure', [KtmTemplateController::class, 'saveSettings'])->name('templates.configure.save');
    Route::post('/templates/{ktmTemplate}/reset-settings', [KtmTemplateController::class, 'resetSettings'])->name('templates.reset-settings');

    // KTM Download Jobs
    Route::get('/download-jobs', \App\Livewire\Admin\DownloadJobs\Index::class)->name('download-jobs.index');
    Route::get('/download-jobs/{id}/download', [\App\Http\Controllers\KtmDownloadJobController::class, 'download'])->name('download-jobs.download');
    Route::delete('/download-jobs/{id}', [\App\Http\Controllers\KtmDownloadJobController::class, 'destroy'])->name('download-jobs.destroy');

    // KTM Generator
    Route::get('/ktm-generator', function () {
        return view('admin.ktm-generator.index');
    })->name('ktm-generator.index');
});

require __DIR__ . '/auth.php';
