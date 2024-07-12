<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subjects', 'subject_id', 'student_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'subject_classrooms')->withPivot('teacher_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_classrooms')
            ->withPivot('classroom_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
