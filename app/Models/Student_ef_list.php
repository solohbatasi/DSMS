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
        'paid_amount',
        'balance',
        'last_payment_date',
        'total_class',
        'type',
        'status'
    ];

    protected $dates = ['last_payment_date'];

    /**
     * EF belongs to a student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * EF belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * EF has many payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'ef_id');
    }
}
