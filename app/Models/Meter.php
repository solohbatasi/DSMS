<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meter_number',
        'meter_type',
        'meter_model',
        'status',
        'customer_id',
        'installation_address',
        'installation_date',
        'last_maintenance_date',
        'initial_reading',
        'notes',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'last_maintenance_date' => 'date',
        'initial_reading' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meter) {
            if (empty($meter->meter_number)) {
                $meter->meter_number = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function latestReading()
    {
        return $this->hasOne(MeterReading::class)->latestOfMany();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeFaulty($query)
    {
        return $query->where('status', 'faulty');
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('installation_address', 'like', "%{$location}%");
    }

    // Accessors
    public function getCurrentReadingAttribute()
    {
        return $this->latestReading ? $this->latestReading->current_reading : $this->initial_reading;
    }

    public function getLastReadingDateAttribute()
    {
        return $this->latestReading ? $this->latestReading->reading_date : null;
    }

    public function getTotalConsumptionAttribute()
    {
        return $this->meterReadings()->sum('consumption');
    }

    public function getIsAssignedAttribute()
    {
        return $this->status === 'assigned' && $this->customer_id !== null;
    }

    // Get initial reading with date
    public function getInitialReadingWithDateAttribute()
    {
        $initialReading = $this->meterReadings()
            ->where('reading_type', 'initial')
            ->first();

        return $initialReading ? [
            'reading' => $initialReading->current_reading,
            'date' => $initialReading->reading_date
        ] : [
            'reading' => $this->initial_reading,
            'date' => $this->installation_date
        ];
    }
}