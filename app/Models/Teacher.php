<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable');
    }
}
