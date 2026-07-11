<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];


    // public function profile()
    // {
    //     return $this->hasOne(Profile::class, 'student_id', 'id');
    // }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'student_id', 'id')->orderBy('id', 'desc');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_id')
            ->withPivot('marks');
    }

    public function detail()
    {
        return $this->hasManyThrough(
            Profile_detail::class,
            Profile::class,
            'student_id', // Foreign key on the Profile table
            'profile_id', // Foreign key on the Profile_detail table
            'id',         // Local key on the Student table
            'id'          // Local key on the Profile table
        );
    }

    public function likes()
    {
        return $this->hasManyThrough(
            Like::class,
            Comment::class,
            'student_id', // Foreign key on the Comment table
            'comment_id', // Foreign key on the Like table
            'id',         // Local key on the Student table
            'id'          // Local key on the Comment table
        );
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable');
    }


}
