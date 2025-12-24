<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('prodi')->nullable()->after('major'); // Program Studi
            $table->string('tempat_lahir')->nullable()->after('prodi'); // Tempat Lahir
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir'); // Tanggal Lahir
            $table->string('angkatan')->nullable()->after('tanggal_lahir'); // Angkatan/Year
            $table->string('jenis_kelamin')->nullable()->after('angkatan'); // L/P
            $table->text('alamat')->nullable()->after('jenis_kelamin'); // Alamat
            $table->string('ktm_file_path')->nullable()->after('ktm_status'); // Path to generated KTM file
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'prodi',
                'tempat_lahir',
                'tanggal_lahir',
                'angkatan',
                'jenis_kelamin',
                'alamat',
                'ktm_file_path',
            ]);
        });
    }
};
