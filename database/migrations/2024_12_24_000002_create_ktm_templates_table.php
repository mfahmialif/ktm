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
        Schema::create('ktm_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('front_template')->nullable();
            $table->string('back_template')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktm_templates');
    }
};
