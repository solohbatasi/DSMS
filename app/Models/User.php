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

  public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
                    ->withTimestamps();
    }
    public function eventsAttending()
    {
        return $this->hasManyThrough(
            Event::class,
            TicketPurchase::class,
            'user_id', // Foreign key on ticket_purchases table
            'id', // Foreign key on events table
            'id', // Local key on users table
            'event_id' // Local key on ticket_purchases table
        )->where('ticket_purchases.status', 'paid');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function votingContests()
    {
        return $this->hasMany(VotingContest::class, 'organizer_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function votePurchases()
    {
        return $this->hasMany(VotePurchase::class);
    }

    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeOrganizers($query)
    {
        return $query->where('role', 'organizer');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeAttendees($query)
    {
        return $query->where('role', 'attendee');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeCanVote($query)
    {
        return $query->where('can_vote', true);
    }

    // Helper Methods
    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    public function isAttendee()
    {
        return $this->role === 'attendee';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getProfileCompletionAttribute()
    {
        $filledFields = 0;
        $totalFields = 0;

        $profileFields = [
            'name',
            'email',
            'phone',
            'bio',
            'avatar',
            // Organizer specific
            'company_name',
            'website',
            'address'
        ];

        foreach ($profileFields as $field) {
            $totalFields++;
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
    }
}
