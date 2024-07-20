<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function studentPayment()
    {
        return $this->belongsTo(StudentPayment::class);
    }

    public function extraCharge()
    {
        return $this->hasMany(ExtraCharge::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
