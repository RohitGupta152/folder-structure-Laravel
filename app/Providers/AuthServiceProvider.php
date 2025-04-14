<?php

namespace App\Providers;

// use App\Models\Student;
use App\Models\User;
// use App\Policies\StudentPolicy;
use App\Policies\UserPolicy;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        User::class => UserPolicy::class,
        // Student::class => StudentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

                // // Define gate for viewing students
                // Gate::define('view-students', function (?User $user = null) {
                //     // Check if user is authenticated
                //     if (!Auth::check()) {
                //         return false;
                //     }
        
                //     // Specific user type check
                //     return $user->user_type === 3;
                // });
                
    }
}
