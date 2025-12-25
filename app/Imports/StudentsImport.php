<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Student([
            'nim'           => $row['nim'],
            'name'          => $row['name'],
            'email'         => $row['email'] ?? null,
            'class'         => $row['class'] ?? null,
            'major'         => $row['major'] ?? null,
            'prodi'         => $row['prodi'],
            'tempat_lahir'  => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'angkatan'      => $row['angkatan'],
            'jenis_kelamin' => $row['jenis_kelamin'], // Expecting L/P
            'alamat'        => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required',
            'name' => 'required',
            'prodi' => 'required',
            'angkatan' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
        ];
    }
}
