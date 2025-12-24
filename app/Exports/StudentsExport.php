<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Student::all();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Name',
            'Email',
            'Class',
            'Major',
            'Prodi',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Angkatan',
            'Jenis Kelamin',
            'Alamat',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nim,
            $student->name,
            $student->email,
            $student->class,
            $student->major,
            $student->prodi,
            $student->tempat_lahir,
            $student->tanggal_lahir ? $student->tanggal_lahir->format('d-m-Y') : null,
            $student->angkatan,
            $student->jenis_kelamin,
            $student->alamat,
        ];
    }
}
