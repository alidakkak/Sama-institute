<?php

namespace App\Providers;

use App\Models\Classroom;
use App\Models\DeviceToken;
use App\Models\Exam;
use App\Models\ExtraCharge;
use App\Models\ImportLog;
use App\Models\InOutLog;
use App\Models\Mark;
use App\Models\Note;
use App\Models\Registration;
use App\Models\Scholarship;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\SubjectClassroom;
use App\Models\Teacher;
use App\Observers\GeneralObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Student::observe(GeneralObserver::class);
        Semester::observe(GeneralObserver::class);
        Subject::observe(GeneralObserver::class);
        Registration::observe(GeneralObserver::class);
        Classroom::observe(GeneralObserver::class);
        SubjectClassroom::observe(GeneralObserver::class);
        StudentSubject::observe(GeneralObserver::class);
        Scholarship::observe(GeneralObserver::class);
        DeviceToken::observe(GeneralObserver::class);
        Exam::observe(GeneralObserver::class);
        ExtraCharge::observe(GeneralObserver::class);
        ImportLog::observe(GeneralObserver::class);
        InOutLog::observe(GeneralObserver::class);
        Mark::observe(GeneralObserver::class);
        Note::observe(GeneralObserver::class);
        StudentPayment::observe(GeneralObserver::class);
        Teacher::observe(GeneralObserver::class);
    }
}
