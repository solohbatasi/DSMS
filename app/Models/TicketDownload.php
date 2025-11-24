<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDownload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'ef_id',
        'session_type',
        'download_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'download_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'download_time',
        'created_at',
        'updated_at',
    ];

    /**
     * Session types enumeration
     */
    const SESSION_TYPES = [
        'practical' => 'practical',
        'theoretical' => 'theoretical'
    ];

    /**
     * Get the student that owns the ticket download.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the EF list that owns the ticket download.
     */
    public function efList(): BelongsTo
    {
        return $this->belongsTo(Student_ef_list::class, 'ef_id');
    }

    /**
     * Get the course through EF list.
     */
    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Student_ef_list::class,
            'id', // Foreign key on Student_ef_list table
            'id', // Foreign key on Course table
            'ef_id', // Local key on TicketDownload table
            'course_id' // Local key on Student_ef_list table
        );
    }

    /**
     * Scope a query to only include today's downloads.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('download_time', today());
    }

    /**
     * Scope a query to only include downloads for a specific session type.
     */
    public function scopeSessionType($query, $sessionType)
    {
        return $query->where('session_type', $sessionType);
    }

    /**
     * Scope a query to only include downloads for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include downloads for a specific EF list.
     */
    public function scopeForEfList($query, $efId)
    {
        return $query->where('ef_id', $efId);
    }

    /**
     * Check if a ticket has already been downloaded for today.
     */
    public static function hasDownloadedToday($studentId, $efId, $sessionType): bool
    {
        return static::forStudent($studentId)
            ->forEfList($efId)
            ->sessionType($sessionType)
            ->today()
            ->exists();
    }

    /**
     * Get today's download count for a session type.
     */
    public static function getTodayDownloadCount($studentId, $efId, $sessionType): int
    {
        return static::forStudent($studentId)
            ->forEfList($efId)
            ->sessionType($sessionType)
            ->today()
            ->count();
    }

    /**
     * Get formatted download time.
     */
    public function getFormattedDownloadTimeAttribute(): string
    {
        return $this->download_time->format('M d, Y h:i A');
    }

    /**
     * Get formatted session type with icon.
     */
    public function getFormattedSessionTypeAttribute(): array
    {
        return [
            'practical' => [
                'label' => 'Practical',
                'color' => 'orange',
                'icon' => '🔧'
            ],
            'theoretical' => [
                'label' => 'Theoretical',
                'color' => 'blue',
                'icon' => '📚'
            ]
        ][$this->session_type] ?? [
            'label' => ucfirst($this->session_type),
            'color' => 'gray',
            'icon' => '❓'
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set download_time to current time when creating
        static::creating(function ($model) {
            if (empty($model->download_time)) {
                $model->download_time = now();
            }
        });
    }
}
