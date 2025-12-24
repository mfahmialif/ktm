<?php

namespace App\Services;

use App\Models\KtmTemplate;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class KtmGeneratorService
{
    protected $template;
    protected $academicYear;
    protected $settings;

    /**
     * Set the template to use for generation
     */
    public function setTemplate(KtmTemplate $template): self
    {
        $this->template = $template;
        $this->settings = $template->settings ?? [];
        return $this;
    }

    /**
     * Set the academic year for folder organization
     */
    public function setAcademicYear(AcademicYear $academicYear): self
    {
        $this->academicYear = $academicYear;
        return $this;
    }

    /**
     * Generate KTM for a single student
     */
    public function generateForStudent(Student $student): array
    {
        if (!$this->template) {
            throw new \Exception('Template not set. Call setTemplate() first.');
        }

        if (!$this->template->front_template) {
            throw new \Exception('Template does not have a front template image.');
        }

        // Load the template image
        $templatePath = Storage::disk('public')->path($this->template->front_template);
        if (!file_exists($templatePath)) {
            throw new \Exception('Template file not found: ' . $templatePath);
        }

        $image = Image::read($templatePath);

        // Apply each enabled field (coordinates are now direct - no scaling needed)
        foreach ($this->settings as $fieldName => $fieldSettings) {
            if (!isset($fieldSettings['enabled']) || !$fieldSettings['enabled']) {
                continue;
            }

            // Determine field type - force 'photo' to be image type
            $type = $fieldSettings['type'] ?? 'text';
            if ($fieldName === 'photo') {
                $type = 'image';
            }

            // Use coordinates directly (no scaling - preview uses actual template size)
            $x = (int) ($fieldSettings['x'] ?? 0);
            $y = (int) ($fieldSettings['y'] ?? 0);

            if ($type === 'image') {
                // Get photo path
                $photoPath = $this->getPhotoPath($student);
                if ($photoPath) {
                    $this->overlayImage($image, $photoPath, $x, $y, $fieldSettings);
                }
            } else {
                // Get text value
                $value = $this->getStudentFieldValue($student, $fieldName);
                if ($value === null || $value === '') continue;

                $this->overlayText($image, (string) $value, $x, $y, $fieldSettings);
            }
        }

        // Generate output path
        $yearFolder = $this->academicYear
            ? str_replace('/', '-', $this->academicYear->code)
            : 'general';

        $outputDir = "ktm/{$yearFolder}";
        $filename = "{$student->nim}.png";
        $outputPath = "{$outputDir}/{$filename}";

        // Ensure directory exists
        Storage::disk('public')->makeDirectory($outputDir);

        // Save the image
        $fullPath = Storage::disk('public')->path($outputPath);
        $image->toPng()->save($fullPath);

        return [
            'success' => true,
            'path' => $outputPath,
            'url' => Storage::url($outputPath),
        ];
    }

    /**
     * Get photo path for student (handles default photo)
     */
    protected function getPhotoPath(Student $student): ?string
    {
        // Try student's photo first
        if (!empty($student->photo)) {
            $photoPath = Storage::disk('public')->path($student->photo);
            if (file_exists($photoPath)) {
                return $photoPath;
            }
        }

        // Try default photos in order of preference
        $defaultPhotos = [
            public_path('img/default_photo.webp'),
            public_path('img/default_photo.png'),
            public_path('img/default_photo.jpg'),
            public_path('img/default_photo.jpeg'),
        ];

        foreach ($defaultPhotos as $defaultPath) {
            if (file_exists($defaultPath)) {
                return $defaultPath;
            }
        }

        return null;
    }

    /**
     * Get field value from student (text fields only)
     */
    protected function getStudentFieldValue(Student $student, string $fieldName): ?string
    {
        // Photo is handled separately by getPhotoPath
        if ($fieldName === 'photo') {
            return null;
        }

        // Get regular field value
        $value = $student->{$fieldName} ?? null;

        // Format special fields
        if ($fieldName === 'tanggal_lahir' && $value) {
            return date('d F Y', strtotime($value));
        }

        return $value;
    }

    /**
     * Overlay text on image
     */
    protected function overlayText($image, string $text, int $x, int $y, array $settings): void
    {
        $fontFamily = $settings['font_family'] ?? 'Arial';
        $fontSize = (int) ($settings['font_size'] ?? 14);
        $fontColor = $settings['font_color'] ?? '#000000';
        $fontWeight = $settings['font_weight'] ?? 'normal';

        // Map font family to font file path (with weight support)
        $fontPath = $this->getFontPath($fontFamily, $fontWeight);

        $image->text($text, $x, $y, function (FontFactory $font) use ($fontPath, $fontSize, $fontColor) {
            $font->filename($fontPath);
            $font->size($fontSize);
            $font->color($fontColor);
        });
    }

    /**
     * Overlay image (photo) on template
     */
    protected function overlayImage($image, string $imagePath, int $x, int $y, array $settings): void
    {
        if (!file_exists($imagePath)) {
            return;
        }

        $width = (int) ($settings['width'] ?? 120);
        $height = (int) ($settings['height'] ?? 160);

        try {
            // Check if it's SVG
            if (str_ends_with(strtolower($imagePath), '.svg')) {
                // For SVG, we skip as Intervention can't handle it directly
                // You might need to convert SVG to PNG first
                return;
            }

            $overlay = Image::read($imagePath);
            $overlay->cover($width, $height);

            $image->place($overlay, 'top-left', $x, $y);
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::warning("Failed to overlay image at {$imagePath}: " . $e->getMessage());
        }
    }

    /**
     * Get system font path with weight support
     */
    protected function getFontPath(string $fontFamily, string $fontWeight = 'normal'): string
    {
        $isBold = $fontWeight === 'bold';

        // Map common font names to system fonts (regular and bold)
        $fontMap = [
            'Arial' => ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf'],
            'Lexend' => ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf'], // Fallback to Arial
            'Roboto' => ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf'],
            'Open Sans' => ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf'],
            'Times New Roman' => ['regular' => 'times.ttf', 'bold' => 'timesbd.ttf'],
        ];

        $fonts = $fontMap[$fontFamily] ?? ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf'];
        $fontFile = $isBold ? $fonts['bold'] : $fonts['regular'];

        // Check common font locations
        $fontPaths = [
            'C:/Windows/Fonts/' . $fontFile,
            '/usr/share/fonts/truetype/' . $fontFile,
            storage_path('fonts/' . $fontFile),
        ];

        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Return Windows default as fallback
        return 'C:/Windows/Fonts/arial.ttf';
    }

    /**
     * Batch generate KTMs for multiple students
     */
    public function generateBatch(array $students): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($students as $student) {
            try {
                $result = $this->generateForStudent($student);
                if ($result['success']) {
                    $student->update([
                        'ktm_status' => 'generated',
                        'ktm_generated_at' => now(),
                        'ktm_file_path' => $result['path'],
                        'ktm_error_message' => null,
                    ]);
                    $results['success']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'student_id' => $student->id,
                    'nim' => $student->nim,
                    'error' => $e->getMessage(),
                ];

                $student->update([
                    'ktm_status' => 'error',
                    'ktm_error_message' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }
}
