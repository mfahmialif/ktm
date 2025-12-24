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
            // Remove old ktm_status columns since we now use student_ktm_statuses table
            $table->dropColumn([
                'ktm_status',
                'ktm_generated_at',
                'ktm_error_message',
                'ktm_file_path',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('ktm_status')->nullable();
            $table->timestamp('ktm_generated_at')->nullable();
            $table->text('ktm_error_message')->nullable();
            $table->string('ktm_file_path')->nullable();
        });
    }
};
