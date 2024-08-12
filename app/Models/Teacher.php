<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_classrooms', 'teacher_id', 'classroom_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'subject_classrooms', 'teacher_id', 'classroom_id');
    }

    public function teacherSalary()
    {
        return $this->hasMany(TeacherSalary::class);
    }
}
