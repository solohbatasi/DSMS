<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_no',
        'user_id',
        'branch_id',
        'name',
        'contact',
        'email',
        'address',
    ];

    public function efLists()
    {
        return $this->hasMany(Student_ef_list::class, 'student_id');
    }
    public function ticketDownloads()
    {
        return $this->hasMany(TicketDownload::class);
    }
        public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
