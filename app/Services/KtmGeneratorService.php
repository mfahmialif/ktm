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
            } elseif ($type === 'custom_text') {
                // Use custom text content
                $text = $fieldSettings['text_content'] ?? '';
                if ($text === '') continue;
                $this->overlayText($image, $text, $x, $y, $fieldSettings);
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
        $align = $settings['text_align'] ?? 'left';
        $lineHeight = (float) ($settings['line_height'] ?? 1.2);

        // Map font family to font file path (with weight support)
        $fontPath = $this->getFontPath($fontFamily, $fontWeight);

        $image->text($text, $x, $y, function (FontFactory $font) use ($fontPath, $fontSize, $fontColor, $align, $lineHeight) {
            $font->filename($fontPath);
            $font->size($fontSize);
            $font->color($fontColor);
            $font->align($align);
            $font->valign('top');
            $font->lineHeight($lineHeight);
        });
    }

    /**
     * Overlay image (photo) on template
     * Uses top-center crop to focus on the upper part of the image (for portraits/faces)
     */
    protected function overlayImage($image, string $imagePath, int $x, int $y, array $settings): void
    {
        if (!file_exists($imagePath)) {
            return;
        }

        $targetWidth = (int) ($settings['width'] ?? 120);
        $targetHeight = (int) ($settings['height'] ?? 160);

        try {
            // Check if it's SVG
            if (str_ends_with(strtolower($imagePath), '.svg')) {
                // For SVG, we skip as Intervention can't handle it directly
                return;
            }

            $overlay = Image::read($imagePath);
            
            // Get original dimensions
            $origWidth = $overlay->width();
            $origHeight = $overlay->height();
            
            // Calculate aspect ratios
            $targetRatio = $targetWidth / $targetHeight;
            $origRatio = $origWidth / $origHeight;
            
            if ($origRatio > $targetRatio) {
                // Original is wider - scale by height, crop width from center
                $newHeight = $targetHeight;
                $newWidth = (int) ($origWidth * ($targetHeight / $origHeight));
                $overlay->resize($newWidth, $newHeight);
                
                // Crop from center horizontally
                $cropX = (int) (($newWidth - $targetWidth) / 2);
                $overlay->crop($targetWidth, $targetHeight, $cropX, 0);
            } else {
                // Original is taller - scale by width, crop height from TOP (focus on face)
                $newWidth = $targetWidth;
                $newHeight = (int) ($origHeight * ($targetWidth / $origWidth));
                $overlay->resize($newWidth, $newHeight);
                
                // Crop from TOP (y = 0) to focus on the face/upper part
                $overlay->crop($targetWidth, $targetHeight, 0, 0);
            }

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
        $bgTransparent = ($settings['bg_transparent'] ?? '1') === '1' || $settings['bg_transparent'] === true;
        $bgColor = $settings['bg_color'] ?? '#ffffff';

        try {
            // Generate barcode PNG
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($content, $generator::TYPE_CODE_128, 2, $height);

            \Illuminate\Support\Facades\Log::info("Barcode generated", ['dataLength' => strlen($barcodeData)]);

            // Create image from barcode data
            $barcodeImage = Image::read($barcodeData);

            // Resize to configured width while maintaining height
            $barcodeImage->resize($width, $height);

            // Apply background color if not transparent
            if (!$bgTransparent) {
                // Create background image with the specified color
                $bgHex = str_replace('#', '', $bgColor);
                $r = hexdec(substr($bgHex, 0, 2));
                $g = hexdec(substr($bgHex, 2, 2));
                $b = hexdec(substr($bgHex, 4, 2));

                $bgImage = imagecreatetruecolor($width, $height);
                $backgroundColor = imagecolorallocate($bgImage, $r, $g, $b);
                imagefill($bgImage, 0, 0, $backgroundColor);

                // Get barcode as GD resource
                $barcodeGd = imagecreatefromstring($barcodeData);
                $barcodeWidth = imagesx($barcodeGd);
                $barcodeHeight = imagesy($barcodeGd);

                // Make white pixels in barcode transparent and copy black pixels
                for ($px = 0; $px < $barcodeWidth; $px++) {
                    for ($py = 0; $py < $barcodeHeight; $py++) {
                        $color = imagecolorat($barcodeGd, $px, $py);
                        $colors = imagecolorsforindex($barcodeGd, $color);
                        // If pixel is black (barcode lines), copy it
                        if ($colors['red'] < 128 && $colors['green'] < 128 && $colors['blue'] < 128) {
                            // Scale coordinates to target size
                            $targetX = (int) ($px * $width / $barcodeWidth);
                            $targetY = (int) ($py * $height / $barcodeHeight);
                            imagesetpixel($bgImage, $targetX, $targetY, imagecolorallocate($bgImage, 0, 0, 0));
                        }
                    }
                }

                imagedestroy($barcodeGd);

                // Convert back to PNG
                ob_start();
                imagepng($bgImage);
                $finalPng = ob_get_clean();
                imagedestroy($bgImage);

                $barcodeImage = Image::read($finalPng);
                $barcodeImage->resize($width, $height);
            }

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
        $bgTransparent = ($settings['bg_transparent'] ?? '1') === '1' || $settings['bg_transparent'] === true;
        $bgColor = $settings['bg_color'] ?? '#ffffff';

        try {
            // Generate QR code using chillerlan/php-qrcode
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'scale' => max(4, intval($size / 25)), // Scale for quality
                'imageBase64' => false,
            ]);

            $qrcode = new QRCode($options);
            $qrCodeData = $qrcode->render($content);

            // Load into GD
            $srcImage = imagecreatefromstring($qrCodeData);
            if ($srcImage === false) {
                throw new \Exception('Failed to create GD image from QR code data');
            }

            $width = imagesx($srcImage);
            $height = imagesy($srcImage);

            // Create a new true color image with alpha support
            $dstImage = imagecreatetruecolor($width, $height);
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);

            // Set background color or transparent
            if ($bgTransparent) {
                $background = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
            } else {
                $bgHex = str_replace('#', '', $bgColor);
                $bgR = hexdec(substr($bgHex, 0, 2));
                $bgG = hexdec(substr($bgHex, 2, 2));
                $bgB = hexdec(substr($bgHex, 4, 2));
                $background = imagecolorallocate($dstImage, $bgR, $bgG, $bgB);
            }
            imagefill($dstImage, 0, 0, $background);

            // Copy pixels
            for ($px = 0; $px < $width; $px++) {
                for ($py = 0; $py < $height; $py++) {
                    $color = imagecolorat($srcImage, $px, $py);
                    $r = ($color >> 16) & 0xFF;
                    $g = ($color >> 8) & 0xFF;
                    $b = $color & 0xFF;

                    // If pixel is white or near-white, use background
                    if ($r > 250 && $g > 250 && $b > 250) {
                        // Already filled with background, skip
                    } else {
                        // Keep the original color (black QR code pixels)
                        if ($bgTransparent) {
                            $newColor = imagecolorallocatealpha($dstImage, $r, $g, $b, 0);
                        } else {
                            $newColor = imagecolorallocate($dstImage, $r, $g, $b);
                        }
                        imagesetpixel($dstImage, $px, $py, $newColor);
                    }
                }
            }

            imagedestroy($srcImage);

            // Convert back to PNG string
            ob_start();
            imagepng($dstImage);
            $finalPng = ob_get_clean();
            imagedestroy($dstImage);

            // Create Intervention Image from PNG
            $qrCodeImage = Image::read($finalPng);

            // Scale to exact configured size
            $qrCodeImage->scale($size, $size);

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
