<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
        'branch_id'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Relationship with branch
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Relationship with courses
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Get active courses
    public function activeCourses(): HasMany
    {
        return $this->courses()->where('status', 1);
    }

    // Get computer category
    public function scopeComputer($query)
    {
        return $query->where('name', 'LIKE', '%computer%');
    }

    // Get driving category
    public function scopeDriving($query)
    {
        return $query->where('name', 'LIKE', '%driving%');
    }

    // Get categories with courses count
    public function getCoursesCountAttribute(): int
    {
        return $this->courses()->count();
    }

    // Get active courses count
    public function getActiveCoursesCountAttribute(): int
    {
        return $this->activeCourses()->count();
    }
}
