<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\KtmTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KtmTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.templates.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('code', 'desc')->get();
        return view('admin.templates.create', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|in:active,incomplete,archived',
        ], [
            'name.required' => 'Nama template wajib diisi.',
        ]);

        $validated['academic_year_id'] = $request->academic_year_id ?: null;

        KtmTemplate::create($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KtmTemplate $ktmTemplate)
    {
        $academicYears = AcademicYear::orderBy('code', 'desc')->get();
        return view('admin.templates.edit', ['template' => $ktmTemplate, 'academicYears' => $academicYears]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KtmTemplate $ktmTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|in:active,incomplete,archived',
        ], [
            'name.required' => 'Nama template wajib diisi.',
        ]);

        $validated['academic_year_id'] = $request->academic_year_id ?: null;

        $ktmTemplate->update($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KtmTemplate $ktmTemplate)
    {
        // Delete files
        if ($ktmTemplate->front_template) {
            Storage::disk('public')->delete($ktmTemplate->front_template);
        }
        if ($ktmTemplate->back_template) {
            Storage::disk('public')->delete($ktmTemplate->back_template);
        }

        $ktmTemplate->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Show the upload form.
     */
    public function showUploadForm(KtmTemplate $ktmTemplate)
    {
        return view('admin.templates.upload', ['template' => $ktmTemplate]);
    }

    /**
     * Upload template backgrounds.
     */
    public function upload(Request $request, KtmTemplate $ktmTemplate)
    {
        $request->validate([
            'front_template' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'back_template' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ], [
            'front_template.image' => 'File harus berupa gambar.',
            'front_template.max' => 'Ukuran file maksimal 5MB.',
            'back_template.image' => 'File harus berupa gambar.',
            'back_template.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($request->hasFile('front_template')) {
            // Delete old file
            if ($ktmTemplate->front_template) {
                Storage::disk('public')->delete($ktmTemplate->front_template);
            }
            $ktmTemplate->front_template = $request->file('front_template')->store('ktm-templates/front', 'public');
        }

        if ($request->hasFile('back_template')) {
            // Delete old file
            if ($ktmTemplate->back_template) {
                Storage::disk('public')->delete($ktmTemplate->back_template);
            }
            $ktmTemplate->back_template = $request->file('back_template')->store('ktm-templates/back', 'public');
        }

        // Update status based on uploads
        if ($ktmTemplate->front_template && $ktmTemplate->back_template) {
            $ktmTemplate->status = 'active';
        } elseif ($ktmTemplate->front_template || $ktmTemplate->back_template) {
            $ktmTemplate->status = 'incomplete';
        }

        $ktmTemplate->save();

        return redirect()->route('templates.index')
            ->with('success', 'Template background berhasil diupload.');
    }

    /**
     * Toggle status between active and archived.
     */
    public function toggleStatus(KtmTemplate $ktmTemplate)
    {
        if ($ktmTemplate->status === 'active') {
            $ktmTemplate->update(['status' => 'archived']);
            $message = 'Template berhasil diarsipkan.';
        } elseif ($ktmTemplate->status === 'archived') {
            $ktmTemplate->update(['status' => 'active']);
            $message = 'Template berhasil diaktifkan.';
        } else {
            return redirect()->route('templates.index')
                ->with('error', 'Template belum lengkap, tidak dapat diaktifkan.');
        }

        return redirect()->route('templates.index')
            ->with('success', $message);
    }

    /**
     * Show the configure fields form.
     */
    public function configure(KtmTemplate $ktmTemplate)
    {
        // Get available fields from Student model
        $studentFields = \App\Models\Student::getKtmFields();
        $availableFields = array_values($studentFields);

        // Get saved settings or empty array
        $savedSettings = $ktmTemplate->settings ?? [];

        // Build fields with settings (only enabled fields have settings)
        // Ensure label and type are always populated from Student model
        $enabledFields = [];
        foreach ($savedSettings as $fieldName => $fieldSettings) {
            if (isset($fieldSettings['enabled']) && $fieldSettings['enabled']) {
                // Make sure label is set from Student model if not in saved data
                if (!isset($fieldSettings['label']) || empty($fieldSettings['label'])) {
                    $fieldSettings['label'] = $studentFields[$fieldName]['label'] ?? \App\Models\Student::getFieldLabel($fieldName);
                }
                // Ensure type is set correctly (especially for photo field)
                if (!isset($fieldSettings['type']) || empty($fieldSettings['type'])) {
                    $fieldSettings['type'] = $studentFields[$fieldName]['type'] ?? 'text';
                }
                // Force photo to be image type
                if ($fieldName === 'photo') {
                    $fieldSettings['type'] = 'image';
                }
                $enabledFields[$fieldName] = $fieldSettings;
            }
        }

        // Get template image dimensions for accurate preview
        $templateWidth = 638;  // Default
        $templateHeight = 400; // Default
        if ($ktmTemplate->front_template) {
            $templatePath = \Illuminate\Support\Facades\Storage::disk('public')->path($ktmTemplate->front_template);
            if (file_exists($templatePath)) {
                $imageSize = @getimagesize($templatePath);
                if ($imageSize) {
                    $templateWidth = $imageSize[0];
                    $templateHeight = $imageSize[1];
                }
            }
        }

        return view('admin.templates.configure', [
            'template' => $ktmTemplate,
            'availableFields' => $availableFields,
            'enabledFields' => $enabledFields,
            'templateWidth' => $templateWidth,
            'templateHeight' => $templateHeight,
        ]);
    }

    /**
     * Save the template field settings.
     */
    public function saveSettings(Request $request, KtmTemplate $ktmTemplate)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.enabled' => 'boolean',
            'settings.*.label' => 'nullable|string',
            'settings.*.type' => 'nullable|string',
            'settings.*.x' => 'nullable|numeric',
            'settings.*.y' => 'nullable|numeric',
            'settings.*.width' => 'nullable|numeric',
            'settings.*.height' => 'nullable|numeric',
            'settings.*.font_family' => 'nullable|string',
            'settings.*.font_size' => 'nullable|numeric',
            'settings.*.font_color' => 'nullable|string',
            'settings.*.font_weight' => 'nullable|string',
            'settings.*.text_align' => 'nullable|string|in:left,center,right',
        ]);

        $ktmTemplate->update([
            'settings' => $validated['settings'],
        ]);

        return redirect()->route('templates.configure', $ktmTemplate)
            ->with('success', 'Pengaturan template berhasil disimpan.');
    }

    /**
     * Reset template settings to default.
     */
    public function resetSettings(KtmTemplate $ktmTemplate)
    {
        $ktmTemplate->update([
            'settings' => null,
        ]);

        return redirect()->route('templates.configure', $ktmTemplate)
            ->with('success', 'Pengaturan template berhasil direset.');
    }
}
