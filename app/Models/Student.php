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
        'name',
        'contact',
        'email',
        'address',
    ];

    public function efLists()
    {
        return $this->hasMany(StudentEfList::class, 'student_id');
    }
}
