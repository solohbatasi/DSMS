<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_code',
        'name',
        'address',
        'contact_phone',
        'contact_email',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the users associated with this branch.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the students associated with this branch.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the courses associated with this branch.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the student EF lists associated with this branch.
     */
    public function studentEfLists()
    {
        return $this->hasMany(StudentEfList::class);
    }

    /**
     * Get the lesson applications associated with this branch.
     */
    public function lessonApplications()
    {
        return $this->hasMany(LessonApplication::class);
    }

    /**
     * Get the expenses associated with this branch.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the payments associated with this branch.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the branch courses mapping (if using separate table).
     */
    public function branchCourses()
    {
        return $this->belongsToMany(Course::class, 'branch_courses')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Get the resources associated with this branch.
     */
    public function resources()
    {
        return $this->hasMany(BranchResource::class);
    }

    /**
     * Scope a query to only include active branches.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive branches.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if the branch is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get branch statistics.
     */
    public function getStatisticsAttribute(): array
    {
        return [
            'students_count' => $this->students()->count(),
            'active_students' => $this->studentEfLists()->where('status', 'active')->count(),
            'total_revenue' => $this->payments()->sum('amount'),
            'total_expenses' => $this->expenses()->sum('amount'),
            'pending_applications' => $this->lessonApplications()->where('status', 'pending')->count(),
            'courses_count' => $this->courses()->count(),
        ];
    }

    /**
     * Get instructors for this branch.
     */
    public function instructors()
    {
        return $this->users()->where('role', 'instructor');
    }

    /**
     * Get staff for this branch.
     */
    public function staff()
    {
        return $this->users()->where('role', 'staff');
    }

    /**
     * Get managers for this branch.
     */
    public function managers()
    {
        return $this->users()->where('role', 'branch_manager');
    }

    /**
     * Get the total students enrolled in a specific period.
     */
    public function enrolledStudentsInPeriod($startDate, $endDate)
    {
        return $this->students()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get the branch's revenue in a specific period.
     */
    public function revenueInPeriod($startDate, $endDate)
    {
        return $this->payments()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get the branch's expenses in a specific period.
     */
    public function expensesInPeriod($startDate, $endDate)
    {
        return $this->expenses()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get available courses for this branch.
     * If using direct branch_id on courses.
     */
    public function availableCourses()
    {
        return $this->courses()->where('status', 1);
    }

    /**
     * Get computer courses for this branch.
     */
    public function computerCourses()
    {
        return $this->courses()
            ->where('course_type', 'computer')
            ->where('status', 1);
    }

    /**
     * Get driving courses for this branch.
     */
    public function drivingCourses()
    {
        return $this->courses()
            ->where('course_type', 'driving')
            ->where('status', 1);
    }

    /**
     * Get other courses for this branch.
     */
    public function otherCourses()
    {
        return $this->courses()
            ->where('course_type', 'other')
            ->where('status', 1);
    }

    /**
     * Get students with pending payments.
     */
    public function studentsWithPendingPayments()
    {
        return $this->students()
            ->whereHas('studentEfLists', function ($query) {
                $query->where('balance', '>', 0);
            });
    }

    /**
     * Get active EF lists.
     */
    public function activeEfLists()
    {
        return $this->studentEfLists()->where('status', 'active');
    }

    /**
     * Get pending EF lists.
     */
    public function pendingEfLists()
    {
        return $this->studentEfLists()->where('status', 'pending');
    }

    /**
     * Find branch by code.
     */
    public static function findByCode($code)
    {
        return static::where('branch_code', $code)->first();
    }

    /**
     * Get branch code with name for display.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->branch_code} - {$this->name}";
    }

    /**
     * Get branch contact information.
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'phone' => $this->contact_phone,
            'email' => $this->contact_email,
            'address' => $this->address,
        ];
    }

    /**
     * Check if user can access this branch.
     */
    public function canBeAccessedBy(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->branch_id === $this->id;
    }

    /**
     * Activate the branch.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the branch.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Get all active resources of a specific type.
     */
    public function getAvailableResources(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->resources()
            ->where('resource_type', $type)
            ->where('status', 'available')
            ->get();
    }
}
