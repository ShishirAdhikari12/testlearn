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
        return $this->belongsTo(Student::class);
    }
}
