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
        Schema::create('student_ef_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('ef_no')->unique();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('total_fee', 10, 2);
            $table->integer('total_class');
            $table->enum('type', [3, 4])->default(3); // 3 = current, 4 = alumni
            $table->enum('status', ['pending','verified','active'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_ef_list');
    }
};
