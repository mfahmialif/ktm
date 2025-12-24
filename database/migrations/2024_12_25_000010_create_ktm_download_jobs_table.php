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
        Schema::create('ktm_download_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ktm_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('download_id')->unique(); // UUID for tracking
            $table->json('filter_criteria')->nullable(); // Stores angkatan, prodi, etc.
            $table->integer('total_files')->default(0);
            $table->integer('processed_files')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('zip_path')->nullable();
            $table->bigInteger('zip_size')->nullable(); // Size in bytes
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktm_download_jobs');
    }
};
