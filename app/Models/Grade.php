<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function academicYear() {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classroom() {
        return $this->hasMany(Classroom::class);
    }
}
