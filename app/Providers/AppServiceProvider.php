<?php

namespace App\Providers;

use App\Repository\StudentRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Interfaces\StudentRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);

        // Register StudentBO as a singleton so it can be resolved from the container
        $this->app->singleton(\App\Modules\Student\BO\StudentBO::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
