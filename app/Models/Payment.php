<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meter_id',
        'user_id',
        'payment_no',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Payment belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    /**
     * Payment belongs to a bill.
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
