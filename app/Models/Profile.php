<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'email',
        'phone',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function profile_detail()
    {
        return $this->hasOne(Profile_detail::class, 'profile_id', 'id');
    }
}
