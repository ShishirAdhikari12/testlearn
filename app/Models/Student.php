<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];


    public function profile()
    {
        return $this->hasOne(Profile::class, 'student_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'student_id', 'id')->orderBy('id', 'desc');
    }

}
