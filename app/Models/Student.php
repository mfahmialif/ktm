<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'ktm_generated_at',
        'ktm_status',
        'ktm_error_message',
        'ktm_file_path',
    ];

    protected $casts = [
        'ktm_generated_at' => 'datetime',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Scope for students pending KTM generation.
     */
    public function scopePending($query)
    {
        return $query->where('ktm_status', 'pending');
    }

    /**
     * Scope for students with generated KTM.
     */
    public function scopeGenerated($query)
    {
        return $query->where('ktm_status', 'generated');
    }

    /**
     * Check if student has generated KTM.
     */
    public function hasKtm(): bool
    {
        return $this->ktm_status === 'generated' && !empty($this->ktm_file_path);
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
