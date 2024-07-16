<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function studentSubject()
    {
        return $this->hasMany(StudentSubject::class);
    }

    public function studentPayments()
    {
        return $this->hasMany(StudentPayment::class, 'semester_id', 'semester_id');
    }

    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class, 'semester_id', 'semester_id');
    }
}
