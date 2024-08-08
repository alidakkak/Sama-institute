<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

//    protected $table = 'device_tokens';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
