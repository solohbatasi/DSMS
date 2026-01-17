<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
         'branch_id',
        'bio',
        'avatar',
        'email_verified_at',
        'is_active',
        'preferences',
        // Organizer fields
        'company_name',
        'company_logo',
        'website',
        'about',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'tax_id',
        'business_registration_number',
        'social_links',
        'business_hours',
        // Vendor fields
        'business_name',
        'contact_number',
        'services_offered',
        // Statistics
        'total_events',
        'total_voting_contests',
        'total_attendees',
        'total_revenue',
        // Verification & rating
        'is_verified',
        'is_featured',
        'rating',
        // Voting fields
        'can_vote',
        'total_votes_cast',
        'total_amount_spent',
        'last_vote_at',
        'voting_preferences',
        // Attendee fields
        'occupation',
        'institution',
        'membership_number',
        'attendee_type',
        // OAuth
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'can_vote' => 'boolean',
        'social_links' => 'array',
        'business_hours' => 'array',
        'preferences' => 'array',
        'voting_preferences' => 'array',
        'total_revenue' => 'decimal:2',
        'total_amount_spent' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_vote_at' => 'datetime',
    ];

    // Relationships
     public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
                    ->withTimestamps();
    }
     /**
     * Get the branch associated with the user.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is branch manager.
     */
    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }

    /**
     * Check if user is instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * Check if user is receptionist/staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Scope a query to only include users from a specific branch.
     */
    public function scopeFromBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }



    /**
     * Check if user can access branch data.
     */
    public function canAccessBranch($branchId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->branch_id == $branchId;
    }

    /**
     * Get accessible branches for the user.
     */
    public function accessibleBranches()
    {
        if ($this->isAdmin()) {
            return Branch::all();
        }

        return Branch::where('id', $this->branch_id)->get();
    }

}
