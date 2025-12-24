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
        Schema::create('batch_activities', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->string('action');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'uploaded'])->default('pending');
            $table->integer('processed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_activities');
    }
};
