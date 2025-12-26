<?php

namespace App\Services;

use App\Models\KtmTemplate;
use App\Models\Student;
use App\Models\StudentKtmStatus;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;
use Picqer\Barcode\BarcodeGeneratorPNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

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
        \Illuminate\Support\Facades\Log::info("KTM Generation settings", ['settings' => $this->settings]);

        foreach ($this->settings as $fieldName => $fieldSettings) {
            if (!isset($fieldSettings['enabled']) || !$fieldSettings['enabled']) {
                continue;
            }

            // Determine field type - force 'photo' to be image type, 'barcode' to be barcode type
            $type = $fieldSettings['type'] ?? 'text';
            if ($fieldName === 'photo') {
                $type = 'image';
            }
            if ($fieldName === 'barcode') {
                $type = 'barcode';
            }
            if ($fieldName === 'qrcode') {
                $type = 'qrcode';
            }

            \Illuminate\Support\Facades\Log::info("Processing field", [
                'fieldName' => $fieldName,
                'type' => $type,
                'originalType' => $fieldSettings['type'] ?? 'not set',
            ]);

            // Use coordinates directly (no scaling - preview uses actual template size)
            $x = (int) ($fieldSettings['x'] ?? 0);
            $y = (int) ($fieldSettings['y'] ?? 0);

            if ($type === 'image') {
                // Get photo path
                $photoPath = $this->getPhotoPath($student);
                if ($photoPath) {
                    $this->overlayImage($image, $photoPath, $x, $y, $fieldSettings);
                }
            } elseif ($type === 'barcode') {
                // Generate barcode with student's NIM
                $this->overlayBarcode($image, $student->nim, $x, $y, $fieldSettings);
            } elseif ($type === 'qrcode') {
                // Generate QR code with student's NIM
                $this->overlayQrCode($image, $student->nim, $x, $y, $fieldSettings);
            } else {
                // Get text value
                $value = $this->getStudentFieldValue($student, $fieldName);
                if ($value === null || $value === '') continue;

                $this->overlayText($image, (string) $value, $x, $y, $fieldSettings);
            }
        }

        // Generate output path - use template name as folder
        $templateFolder = \Illuminate\Support\Str::slug($this->template->name);

        $outputDir = "ktm/{$templateFolder}";
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

        // Add font size to Y to simulate top-left positioning
        // Intervention Image positions text by baseline, CSS positions by top
        $adjustedY = $y + $fontSize;

        $image->text($text, $x, $adjustedY, function (FontFactory $font) use ($fontPath, $fontSize, $fontColor) {
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
     * Overlay barcode on template
     */
    protected function overlayBarcode($image, string $content, int $x, int $y, array $settings): void
    {
        \Illuminate\Support\Facades\Log::info("overlayBarcode called", [
            'content' => $content,
            'x' => $x,
            'y' => $y,
            'width' => $settings['width'] ?? 200,
            'height' => $settings['height'] ?? 50,
        ]);

        if (empty($content)) {
            \Illuminate\Support\Facades\Log::warning("overlayBarcode: empty content, skipping");
            return;
        }

        $width = (int) ($settings['width'] ?? 200);
        $height = (int) ($settings['height'] ?? 50);

        try {
            // Generate barcode PNG
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($content, $generator::TYPE_CODE_128, 2, $height);

            \Illuminate\Support\Facades\Log::info("Barcode generated", ['dataLength' => strlen($barcodeData)]);

            // Create image from barcode data
            $barcodeImage = Image::read($barcodeData);

            // Resize to configured width while maintaining height
            $barcodeImage->resize($width, $height);

            // Place barcode on template
            $image->place($barcodeImage, 'top-left', $x, $y);

            \Illuminate\Support\Facades\Log::info("Barcode placed successfully at ({$x}, {$y})");
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::warning("Failed to generate barcode for content '{$content}': " . $e->getMessage());
        }
    }

    /**
     * Overlay QR code on template
     */
    protected function overlayQrCode($image, string $content, int $x, int $y, array $settings): void
    {
        if (empty($content)) {
            return;
        }

        $size = (int) ($settings['width'] ?? 100);

        try {
            // Generate QR code using chillerlan/php-qrcode
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'scale' => max(1, intval($size / 25)), // Scale based on desired size
                'imageBase64' => false,
            ]);

            $qrcode = new QRCode($options);
            $qrCodeData = $qrcode->render($content);

            // Create image from QR code PNG data
            $qrCodeImage = Image::read($qrCodeData);

            // Resize to exact configured size
            $qrCodeImage->resize($size, $size);

            // Place QR code on template
            $image->place($qrCodeImage, 'top-left', $x, $y);

            \Illuminate\Support\Facades\Log::info("QR code placed successfully at ({$x}, {$y})");
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::warning("Failed to generate QR code for content '{$content}': " . $e->getMessage());
        }
    }

    /**
     * Get system font path with weight support
     */
    protected function getFontPath(string $fontFamily, string $fontWeight = 'normal'): string
    {
        $isBold = $fontWeight === 'bold';

        /*
     * Font mapping (SEMUA MENGARAH KE FONT INTERNAL PROJECT)
     * Jangan tergantung OS
     */
        $fontMap = [
            'Arial' => [
                'regular' => 'arial.ttf',
                'bold'    => 'arial-bold.ttf',
            ],
            'Lexend' => [
                'regular' => 'lexend-regular.ttf',
                'bold'    => 'lexend-bold.ttf',
            ],
            'Roboto' => [
                'regular' => 'roboto-regular.ttf',
                'bold'    => 'roboto-bold.ttf',
            ],
            'Open Sans' => [
                'regular' => 'opensans-regular.ttf',
                'bold'    => 'opensans-bold.ttf',
            ],
            'Times New Roman' => [
                'regular' => 'times.ttf',
                'bold'    => 'timesbd.ttf',
            ],
        ];

        // Default fallback font (AMAN DI SEMUA SERVER)
        $defaultFont = [
            'regular' => 'arial.ttf',
            'bold'    => 'arial-bold.ttf',
        ];

        $fonts = $fontMap[$fontFamily] ?? $defaultFont;
        $fontFile = $isBold ? $fonts['bold'] : $fonts['regular'];

        // PRIORITAS PATH (INTERNAL PROJECT DULU)
        $fontPaths = [
            storage_path('fonts/' . $fontFile),
            resource_path('fonts/' . $fontFile), // opsional jika Anda simpan di resources
            base_path('fonts/' . $fontFile),     // opsional
        ];

        foreach ($fontPaths as $path) {
            if (is_readable($path)) {
                return $path;
            }
        }

        /*
     * FINAL FALLBACK (PASTI ADA)
     * Pastikan file ini BENAR-BENAR ADA di storage/fonts
     */
        $safeFallback = storage_path('fonts/arial.ttf');

        if (! is_readable($safeFallback)) {
            throw new \RuntimeException(
                "Font file not found. Please ensure fonts exist in storage/fonts"
            );
        }

        return $safeFallback;
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
                    // Save status to pivot table
                    StudentKtmStatus::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'ktm_template_id' => $this->template->id,
                        ],
                        [
                            'status' => 'generated',
                            'file_path' => $result['path'],
                            'error_message' => null,
                            'generated_at' => now(),
                        ]
                    );
                    $results['success']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'student_id' => $student->id,
                    'nim' => $student->nim,
                    'error' => $e->getMessage(),
                ];

                // Save error status to pivot table
                StudentKtmStatus::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'ktm_template_id' => $this->template->id,
                    ],
                    [
                        'status' => 'error',
                        'error_message' => $e->getMessage(),
                    ]
                );
            }
        }

        return $results;
    }
}
