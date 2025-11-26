<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course',
        'image',
        'description',
        'training_duration',
        'practical_sessions',
        'theoretical_sessions',
        'total_amount',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'practical_sessions' => 'integer',
        'theoretical_sessions' => 'integer',
        'total_amount' => 'decimal:2',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
        'status_text',
        'formatted_fee',
        'enrolled_students_count',
        'can_delete',
        'practical_percentage',
        'theoretical_percentage',
    ];

    /**
     * Relationship with Student_ef_list
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function studentEfLists(): HasMany
    {
        return $this->hasMany(Student_ef_list::class, 'course_id');
    }
    /**
     * Get practical sessions percentage
     *
     * @return float
     */
    public function getPracticalPercentageAttribute(): float
    {
        $total = $this->practical_sessions + $this->theoretical_sessions;
        return $total > 0 ? round(($this->practical_sessions / $total) * 100, 1) : 0;
    }

    /**
     * Get theoretical sessions percentage
     *
     * @return float
     */
    public function getTheoreticalPercentageAttribute(): float
    {
        $total = $this->practical_sessions + $this->theoretical_sessions;
        return $total > 0 ? round(($this->theoretical_sessions / $total) * 100, 1) : 0;
    }

    /**
     * Get session summary
     *
     * @return string
     */
    public function getSessionSummaryAttribute(): string
    {
        $total = $this->practical_sessions + $this->theoretical_sessions;
        return "Practical: {$this->practical_sessions} sessions ({$this->practical_percentage}%) | Theory: {$this->theoretical_sessions} sessions ({$this->theoretical_percentage}%)";
    }

    /**
     * Get total sessions (calculated)
     *
     * @return int
     */
    public function getTotalSessionsAttribute(): int
    {
        return $this->practical_sessions + $this->theoretical_sessions;
    }

    /**
     * Validate that practical + theoretical sessions match training duration
     *
     * @return bool
     */
    public function validateSessions(): bool
    {
        // Extract number from training_duration (e.g., "21 days" -> 21)
        preg_match('/(\d+)/', $this->training_duration, $matches);
        $duration = $matches[1] ?? 0;

        return ($this->practical_sessions + $this->theoretical_sessions) == $duration;
    }

    /**
     * Get recommended session breakdown based on course type
     *
     * @return array
     */
    public function getRecommendedBreakdownAttribute(): array
    {
        $courseName = strtolower($this->course);

        if (str_contains($courseName, 'beginner') || str_contains($courseName, 'basic')) {
            return ['practical' => 70, 'theoretical' => 30];
        } elseif (str_contains($courseName, 'commercial') || str_contains($courseName, 'heavy')) {
            return ['practical' => 60, 'theoretical' => 40];
        } elseif (str_contains($courseName, 'motorcycle')) {
            return ['practical' => 80, 'theoretical' => 20];
        } elseif (str_contains($courseName, 'refresher')) {
            return ['practical' => 90, 'theoretical' => 10];
        } else {
            return ['practical' => 65, 'theoretical' => 35]; // Default
        }
    }
    /**
     * Get the enrolled students count
     *
     * @return int
     */
    public function getEnrolledStudentsCountAttribute(): int
    {
        return $this->studentEfLists()->count();
    }

    /**
     * Get the full image URL
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

       // Direct URL using asset helper
        return asset('storage/' . $this->image);
    }
    public function getImageExistsAttribute(): bool
    {
        if (!$this->image) {
            return false;
        }

        return Storage::disk('public')->exists($this->image);
    }
    /**
     * Get the status as text
     *
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }

    /**
     * Get the formatted fee
     *
     * @return string
     */
    public function getFormattedFeeAttribute(): string
    {
        return 'KSh ' . number_format($this->total_amount, 2);
    }

    /**
     * Get the short description (truncated)
     *
     * @return string
     */
    public function getShortDescriptionAttribute(): string
    {
        if (!$this->description) {
            return 'No description available';
        }

        return strlen($this->description) > 100
            ? substr($this->description, 0, 100) . '...'
            : $this->description;
    }

    /**
     * Check if the course can be deleted
     * (Cannot delete if there are enrolled students)
     *
     * @return bool
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->studentEfLists()->count() === 0;
    }

    /**
     * Scope a query to only include active courses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include inactive courses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope a query to order by fee (high to low).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByFeeHighToLow($query)
    {
        return $query->orderBy('total_amount', 'desc');
    }

    /**
     * Scope a query to order by fee (low to high).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByFeeLowToHigh($query)
    {
        return $query->orderBy('total_amount', 'asc');
    }

    /**
     * Scope a query to search courses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('course', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('training_duration', 'like', "%{$search}%");
        });
    }

    /**
     * Get popular courses (by fee)
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopularCourses($limit = 5)
    {
        return self::active()
            ->orderBy('total_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Activate the course
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['status' => 1]);
    }

    /**
     * Deactivate the course
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 0]);
    }

    /**
     * Update course image
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return bool
     */
    public function updateImage($image): bool
    {
        // Delete old image if exists
        if ($this->image) {
            Storage::disk('public')->delete($this->image);
        }

        // Store new image
        $imagePath = $image->store('course-images', 'public');

        return $this->update(['image' => $imagePath]);
    }

    /**
     * Delete course image
     *
     * @return bool
     */
    public function deleteImage(): bool
    {
        if (!$this->image) {
            return true;
        }

        Storage::disk('public')->delete($this->image);

        return $this->update(['image' => null]);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Delete image when course is deleted
        static::deleting(function ($course) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
        });
    }

    /**
     * Get courses with student count
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function withStudentCount()
    {
        return self::withCount('studentEfLists')->get();
    }

    /**
     * Check if course has enrolled students
     *
     * @return bool
     */
    public function hasEnrolledStudents(): bool
    {
        return $this->studentEfLists()->exists();
    }

    /**
     * Get course statistics
     *
     * @return array
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'active' => self::active()->count(),
            'inactive' => self::inactive()->count(),
            'total_revenue' => self::active()->sum('total_amount'),
        ];
    }
}
