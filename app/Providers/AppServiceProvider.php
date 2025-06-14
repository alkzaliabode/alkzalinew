<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\SanitationFacilityTask;
use App\Observers\SanitationFacilityTaskObserver;
use App\Models\GeneralCleaningTask;
use App\Observers\GeneralCleaningTaskObserver;
use App\Models\EmployeeTask;
use App\Observers\EmployeeTaskObserver;

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
        Schema::defaultStringLength(191);

        // تسجيل جميع الـ Observers
       \App\Models\SanitationFacilityTask::observe(\App\Observers\SanitationFacilityTaskObserver::class);
    \App\Models\GeneralCleaningTask::observe(\App\Observers\GeneralCleaningTaskObserver::class);
    \App\Models\EmployeeTask::observe(\App\Observers\EmployeeTaskObserver::class);
    }
}