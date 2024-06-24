<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_classrooms', 'classroom_id', 'student_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_classrooms')->withPivot('teacher_id');
    }
}
