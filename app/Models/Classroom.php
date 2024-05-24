<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function grade() {
        return $this->belongsTo(Grade::class);
    }

    public function students() {
        return $this->belongsToMany(Student::class, 'student_classrooms', 'classroom_id', 'student_id');
    }
}
