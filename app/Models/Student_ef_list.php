<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student_ef_list extends Model
{
    protected $fillable = [
        'student_id',
        'ef_no',
        'course_id',
        'total_fee',
        'total_class',
        'type',
    ];

    public function student()
{
    return $this->belongsTo(Student::class);
}

public function course()
{
    return $this->belongsTo(Course::class);
}

public function payments()
{
    return $this->hasMany(Payment::class, 'ef_id');
}

}
