<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.academic-years.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic-years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:academic_years,code',
            'name' => 'required|string|max:255',
            'semester' => 'required|in:ganjil,genap',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Kode wajib diisi.',
            'code.unique' => 'Kode sudah digunakan.',
            'name.required' => 'Nama tahun akademik wajib diisi.',
            'semester.required' => 'Semester wajib dipilih.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // If this is set as active, deactivate others
        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:academic_years,code,' . $academicYear->id,
            'name' => 'required|string|max:255',
            'semester' => 'required|in:ganjil,genap',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Kode wajib diisi.',
            'code.unique' => 'Kode sudah digunakan.',
            'name.required' => 'Nama tahun akademik wajib diisi.',
            'semester.required' => 'Semester wajib dipilih.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // If this is set as active, deactivate others
        if ($validated['is_active'] && !$academicYear->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(AcademicYear $academicYear)
    {
        if (!$academicYear->is_active) {
            // Deactivate all others first
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear->update(['is_active' => !$academicYear->is_active]);

        return redirect()->route('academic-years.index')
            ->with('success', $academicYear->is_active ? 'Tahun akademik diaktifkan.' : 'Tahun akademik dinonaktifkan.');
    }
}
