<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.students.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|unique:students,nim',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'prodi' => 'required|string|max:255',
            'class' => 'nullable|string|max:50',
            'angkatan' => 'required|string|max:4',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students/photos', 'public');
            $validated['photo'] = $path;
        }

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Student data created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nim' => 'required|string|unique:students,nim,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'prodi' => 'required|string|max:255',
            'class' => 'nullable|string|max:50',
            'angkatan' => 'required|string|max:4',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('students/photos', 'public');
            $validated['photo'] = $path;
        }

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Student data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
