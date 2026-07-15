<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
    ];

    // public function students()
    // {
    //     return $this->belongsToMany(Student::class, 'student_subject', 'subject_id', 'student_id')
    //     ->withPivot('marks');
    // }

    public function students()
    {
        return $this->morphedByMany(Student::class, 'courseable');
    }

    public function teachers()
    {
        return $this->morphedByMany(Teacher::class, 'courseable');
    }
}
