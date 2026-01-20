<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student_ef_list extends Model
{
    protected $table = 'student_ef_lists'; // Explicitly set table name

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
        'status',
        'attended_practical',
        'remaining_practical',
        'attended_theoretical',
        'remaining_theoretical',
         'branch_id',
    ];

    protected $dates = ['last_payment_date'];

    /**
     * EF belongs to a student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * EF belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function branch()
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

    /**
     * EF has many ticket downloads
     */
    public function ticketDownloads()
    {
        return $this->hasMany(TicketDownload::class, 'ef_id');
    }
     /**
     * Get total practical sessions
     */
    public function getTotalPracticalAttribute()
    {
        return $this->attended_practical + $this->remaining_practical;
    }

    /**
     * Get total theoretical sessions
     */
    public function getTotalTheoreticalAttribute()
    {
        return $this->attended_theoretical + $this->remaining_theoretical;
    }

    /**
     * Check if practical sessions are available
     */
    public function hasPracticalSessions()
    {
        return $this->remaining_practical > 0;
    }

    /**
     * Check if theoretical sessions are available
     */
    public function hasTheoreticalSessions()
    {
        return $this->remaining_theoretical > 0;
    }

    /**
     * Get practical session progress percentage
     */
    public function getPracticalProgressAttribute()
    {
        $total = $this->total_practical;
        return $total > 0 ? ($this->attended_practical / $total) * 100 : 0;
    }

    /**
     * Get theoretical session progress percentage
     */
    public function getTheoreticalProgressAttribute()
    {
        $total = $this->total_theoretical;
        return $total > 0 ? ($this->attended_theoretical / $total) * 100 : 0;
    }
}
