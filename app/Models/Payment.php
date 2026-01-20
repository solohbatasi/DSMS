<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'ef_id',
        'amount',
        'balance',
        'payment_date',
        'remarks',
        'payment_status',
        'payment_method',
         'branch_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Payment belongs to a StudentEfList through ef_id
     */
    public function efList()
    {
        return $this->belongsTo(Student_ef_list::class, 'ef_id');
    }

    /**
     * Payment recorded by a system user (staff/admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }
}
