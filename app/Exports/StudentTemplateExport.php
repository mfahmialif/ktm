<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Return a single sample row collection
        return collect([
            (object)[
                'nim' => '20240001',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'class' => 'IF-A',
                'major' => 'Informatics',
                'prodi' => 'Teknik Informatika',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-01-01',
                'angkatan' => '2024',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Sudirman No. 1',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
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
        ];
    }

    public function map($row): array
    {
        return [
            $row->nim,
            $row->name,
            $row->email,
            $row->class,
            $row->major,
            $row->prodi,
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->angkatan,
            $row->jenis_kelamin,
            $row->alamat,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
