<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
