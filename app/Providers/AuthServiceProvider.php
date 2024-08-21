<?php

namespace App\Providers;

use App\Policies\DeletePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-all', function ($user) {
            return $user->id === 1;
        });

        Gate::define('admin', function ($user) {
            return $user->roles->contains(function ($role) {
                return str_contains(strtolower($role->name), 'admin');
            });
        });
            // Deletion Policy ///
            Gate::define('delete', [DeletePolicy::class, 'delete']);
        
    }
}
