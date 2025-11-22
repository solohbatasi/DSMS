<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'current_reading',
        'previous_reading',
        'consumption',
        'reading_date',
        'reading_type',
        'reading_period',
        'billed',
        'billed_by',
        'billed_at',
        'notes',
        'reading_image',
        'read_by',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'current_reading' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'consumption' => 'decimal:2',
        'billed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reading) {
            // Auto-calculate consumption
            $reading->consumption = $reading->current_reading - $reading->previous_reading;
            
            // Auto-set reading period if not provided
            if (empty($reading->reading_period)) {
                $reading->reading_period = \Carbon\Carbon::parse($reading->reading_date)->format('F Y');
            }
        });

        static::updating(function ($reading) {
            // Recalculate consumption if readings change
            if ($reading->isDirty(['current_reading', 'previous_reading'])) {
                $reading->consumption = $reading->current_reading - $reading->previous_reading;
            }
        });
    }

      // Add this to allow null customer_id
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['customer_id'] = $this->attributes['customer_id'] ?? null;
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function biller()
    {
        return $this->belongsTo(User::class, 'billed_by');
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    // Scopes
    public function scopeMonthly($query)
    {
        return $query->where('reading_type', 'monthly');
    }

    public function scopeBilled($query)
    {
        return $query->where('billed', true);
    }

    public function scopeUnbilled($query)
    {
        return $query->where('billed', false);
    }

    public function scopeForPeriod($query, $period)
    {
        return $query->where('reading_period', $period);
    }

    public function scopeCurrentPeriod($query)
    {
        return $query->where('reading_period', now()->format('F Y'));
    }

    // Helper methods
    public function getFormattedConsumptionAttribute()
    {
        return number_format($this->consumption, 2) . ' m³';
    }

    public function isBillable()
    {
        return !$this->billed && $this->reading_type === 'monthly' && $this->consumption > 0;
    }
}