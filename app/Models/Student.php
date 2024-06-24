<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $guard = 'api_student';

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'student_classrooms', 'student_id', 'classroom_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
