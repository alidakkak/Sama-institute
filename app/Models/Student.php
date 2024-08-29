<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $guard = 'api_student';

    public function setImageAttribute($image)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $newImageName = uniqid().'_'.'students_image'.'.'.$image->extension();
            $image->move(public_path('students_image'), $newImageName);

            return $this->attributes['image'] = '/'.'students_image'.'/'.$newImageName;
        } elseif (is_null($image)) {
            $this->attributes['image'] = '/default_image/female.jpg';
        } elseif (is_string($image)) {
            $this->attributes['image'] = $image;
        }
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function studentPayment()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function extraCharge()
    {
        return $this->hasMany(ExtraCharge::class);
    }

    public function deviceToken()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function inOutLog()
    {
        return $this->hasMany(InOutLog::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
