<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'physical_address',
        'plot_number',
        'house_number',
        'estate',
        'meter_number',
        'meter_type',
        'connection_type',
        'initial_meter_reading',
        'connection_date',
        'status',
        'kra_pin',
        'property_owner',
        'expected_users',
        'notes',
    ];

    protected $casts = [
        'connection_date' => 'date',
        'initial_meter_reading' => 'decimal:2',
        'expected_users' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = 'CUST' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function waterApplication()
    {
        return $this->hasOne(WaterConnectionApplication::class);
    }

    public function meter()
    {
        return $this->hasOne(Meter::class);
    }

     public function meterReadings()
    {
        return $this->hasMany(MeterReading::class)->orderBy('reading_date', 'desc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'customer_id')->orderBy('created_at', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id')->orderBy('payment_date', 'desc');
    }

    // Account balance calculation
    public function getAccountBalanceAttribute()
    {
        $totalPaid = $this->payments()->sum('amount');
        $totalBilled = $this->bills()->sum('total_amount');
        return $totalPaid - $totalBilled;
    }

    // Get current outstanding balance (unpaid bills)
    public function getOutstandingBalanceAttribute()
    {
        return $this->bills()->where('bill_status', 'unpaid')->sum('total_amount');
    }

    // Get total consumption
    public function getTotalConsumptionAttribute()
    {
        return $this->meterReadings()->sum('consumption');
    }

    // Get average monthly consumption
    public function getAverageMonthlyConsumptionAttribute()
    {
        $readingsCount = $this->meterReadings()->count();
        if ($readingsCount === 0) return 0;

        return $this->total_consumption / $readingsCount;
    }

    // Get recent bills (last 6 months)
    public function recentBills($limit = 6)
    {
        return $this->bills()->with('payments')->latest()->limit($limit)->get();
    }

    // Get payment history
    public function paymentHistory($limit = 10)
    {
        return $this->payments()->latest()->limit($limit)->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->plot_number . ', ' . $this->house_number;
        if ($this->estate) {
            $address .= ', ' . $this->estate;
        }
        return $address;
    }

    public function getCurrentMeterReadingAttribute()
    {
        $latestReading = $this->meterReadings()->latest()->first();
        return $latestReading ? $latestReading->current_reading : $this->initial_meter_reading;
    }

    public function getLastReadingDateAttribute()
    {
        $latestReading = $this->meterReadings()->latest()->first();
        return $latestReading ? $latestReading->reading_date : null;
    }

    // Static methods for dropdowns
    public static function getConnectionTypes()
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'public' => 'Public Institution',
        ];
    }

    public static function getAvailableMeterTypes()
    {
        return [
            'domestic' => 'Domestic - Single Phase',
            'commercial' => 'Commercial - Three Phase',
            'industrial' => 'Industrial - High Capacity',
            'institutional' => 'Institutional - Bulk Meter',
        ];
    }
}
