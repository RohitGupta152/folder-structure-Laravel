<?php

namespace App\Providers;

use App\Repository\AuthRepository;
use App\Repository\Interfaces\AuthRepositoryInterface;
use App\Repository\Interfaces\OrderRepositoryInterface;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Repository\Interfaces\RateChartRepositoryInterface;
use App\Repository\StudentRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Interfaces\StudentRepositoryInterface;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\RateChartRepository;
use App\Repository\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);

        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(RateChartRepositoryInterface::class, RateChartRepository::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

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
