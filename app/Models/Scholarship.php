<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
