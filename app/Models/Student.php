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
    
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_id')
        ->withPivot('marks');
    }

}


