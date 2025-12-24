<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'name',
        'email',
        'class',
        'major',
        'prodi',
        'tempat_lahir',
        'tanggal_lahir',
        'angkatan',
        'jenis_kelamin',
        'alamat',
        'photo',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get all KTM statuses for this student.
     */
    public function ktmStatuses(): HasMany
    {
        return $this->hasMany(StudentKtmStatus::class);
    }

    /**
     * Get KTM status for a specific template.
     */
    public function getKtmStatusForTemplate(int $templateId): ?StudentKtmStatus
    {
        return $this->ktmStatuses()->where('ktm_template_id', $templateId)->first();
    }

    /**
     * Check if student has generated KTM for a specific template.
     */
    public function hasKtmForTemplate(int $templateId): bool
    {
        $status = $this->getKtmStatusForTemplate($templateId);
        return $status && $status->isGenerated();
    }

    /**
     * Get the status string for a specific template.
     */
    public function getStatusForTemplate(?int $templateId): string
    {
        if (!$templateId) {
            return empty($this->photo) ? 'no_photo' : 'ready';
        }

        $status = $this->getKtmStatusForTemplate($templateId);

        if (!$status) {
            return empty($this->photo) ? 'no_photo' : 'ready';
        }

        if ($status->status === 'error') {
            return 'error';
        }

        if ($status->status === 'generated') {
            return 'generated';
        }

        return empty($this->photo) ? 'no_photo' : 'ready';
    }

    /**
     * Get tempat tanggal lahir formatted.
     */
    public function getTempatTanggalLahirAttribute(): string
    {
        if ($this->tempat_lahir && $this->tanggal_lahir) {
            return $this->tempat_lahir . ', ' . $this->tanggal_lahir->format('d F Y');
        }
        return '-';
    }

    /**
     * Get available fields for KTM template.
     * Returns columns that can be displayed on KTM card.
     */
    public static function getKtmFields(): array
    {
        // Columns to exclude from KTM fields
        $excludedColumns = [
            'id',
            'created_at',
            'updated_at',
            'ktm_generated_at',
            'ktm_status',
            'ktm_error_message',
            'ktm_file_path',
        ];

        // Get all columns from the students table
        $columns = Schema::getColumnListing('students');

        // Filter out excluded columns
        $ktmFields = array_filter($columns, fn($col) => !in_array($col, $excludedColumns));

        // Create field definitions with labels
        $fields = [];
        foreach ($ktmFields as $column) {
            $fields[$column] = [
                'column' => $column,
                'label' => self::getFieldLabel($column),
                'type' => $column === 'photo' ? 'image' : 'text',
            ];
        }

        return $fields;
    }

    /**
     * Get human-readable label for a column.
     */
    public static function getFieldLabel(string $column): string
    {
        $labels = [
            'nim' => 'NIM',
            'name' => 'Nama Mahasiswa',
            'email' => 'Email',
            'class' => 'Kelas',
            'major' => 'Jurusan',
            'prodi' => 'Program Studi',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'angkatan' => 'Angkatan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'alamat' => 'Alamat',
            'photo' => 'Foto',
        ];

        return $labels[$column] ?? ucwords(str_replace('_', ' ', $column));
    }

    /**
     * Get sample data for a field (for preview).
     */
    public static function getSampleData(string $column): string
    {
        $samples = [
            'nim' => '210510001',
            'name' => 'Amanda Pratiwi',
            'email' => 'amanda@university.edu',
            'class' => 'IF-A',
            'major' => 'Teknik Informatika',
            'prodi' => 'Teknik Informatika',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '15 Mei 2003',
            'angkatan' => '2021',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Contoh No. 123',
            'photo' => '',
        ];

        return $samples[$column] ?? 'Sample ' . ucwords(str_replace('_', ' ', $column));
    }
}
