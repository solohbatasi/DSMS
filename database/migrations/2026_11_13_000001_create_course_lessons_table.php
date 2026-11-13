<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('section')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 20);
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('file_size');
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
