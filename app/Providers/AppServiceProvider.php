<?php

namespace App\Providers;

use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
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
    }
}
