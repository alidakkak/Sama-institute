<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function students() {
        return $this->belongsToMany(Student::class, 'student_subjects', 'subject_id', 'student_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teachers', 'subject_id', 'teacher_id');
    }
}
