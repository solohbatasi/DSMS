<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lesson_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();

            // Student information (can be existing student or new applicant)
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ef_list_id')->nullable()->constrained('student_ef_lists')->onDelete('cascade');

            // For new applicants (not yet in system)
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('id_number')->nullable();
            $table->text('address')->nullable();

            // Course and scheduling information
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('preferred_start_date');
            $table->enum('preferred_schedule', ['weekday_morning', 'weekday_afternoon', 'weekday_evening', 'weekend_morning', 'weekend_afternoon', 'flexible']);
            $table->text('special_requirements')->nullable();

            // Application status and tracking
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'enrolled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_applications');
    }
};
