<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'instructor_id',
        'instructor_effectiveness',
        'content_quality',
        'resources_quality',
        'what_you_liked',
        'improvement_suggestions',
        'instructor_feedback',
        'would_recommend',
        'is_anonymous'
    ];

    protected $casts = [
        'instructor_effectiveness' => 'integer',
        'content_quality' => 'integer',
        'resources_quality' => 'integer',
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
