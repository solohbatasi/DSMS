<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'student_id',
        'ef_list_id',
        'full_name',
        'email',
        'phone',
        'id_number',
        'address',
        'course_id',
        'preferred_start_date',
        'preferred_schedule',
        'special_requirements',
        'status',
        'admin_notes',
        'contacted_at',
        'reviewed_at',
        'reviewed_by',
         'branch_id',
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
        'contacted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function efList()
    {
        return $this->belongsTo(StudentEfList::class, 'ef_list_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function markAsContacted()
    {
        $this->update(['contacted_at' => now()]);
    }

    public function markAsUnderReview($adminId = null)
    {
        $this->update([
            'status' => 'under_review',
            'reviewed_by' => $adminId,
            'reviewed_at' => now()
        ]);
    }

    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes
        ]);
    }

    // Generate application number
    public static function generateApplicationNumber()
    {
        $prefix = 'APP';
        $date = now()->format('Ymd');
        $lastApp = self::where('application_number', 'like', $prefix . $date . '%')->latest()->first();

        $sequence = $lastApp ? (int)substr($lastApp->application_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
