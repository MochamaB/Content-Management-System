<?php

namespace App\Providers;

use App\Models\SettingSite;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\Lease;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Scopes\UnitAccessScope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
          // Apply the UnitAccessScope to specific models
        Lease::addGlobalScope(new UnitAccessScope);
        
    //    Unit::addGlobalScope(new UnitAccessScope);
    // Invoice::addGlobalScope(new UnitAccessScope);


        /////////// GLOBAL VIEW COMPOSERS
        view()->composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            $routeParts = explode('.', $routeName);
            $urlParts = explode('/', url()->current());
    
            $view->with([
                'routeName' => $routeName,
                'routeParts' => $routeParts,
                'urlParts' => $urlParts,
            ]);
        });

        //////////////  FRONT END/////////////
        view()->composer('layouts.client.navbar', function($view) {
            $sitesettings = SettingSite::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('layouts.client.footer', function($view) {
            $sitesettings = SettingSite::first();
            $view->with(['sitesettings' => $sitesettings]);
        });

        view()->composer('client.slider', function($view) {
            $slider = Slider::all();
            $view->with(['slider' => $slider]);
        });

        
        view()->composer('client.testimonial', function($view) {
            $testimonial = Testimonial::all();
            $view->with(['testimonial' => $testimonial]);
        });

        

        /////////ADMIN//////////////////////////////

        view()->composer('layouts.admin.adminheader', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = SettingSite::first();
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user
                ,'sitesettings' => $sitesettings]);
        });

        view()->composer('layouts.admin.adminnavbar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = SettingSite::first();
            $notifications = $user->notifications;
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user
                ,'sitesettings' => $sitesettings,
                'notifications' =>$notifications]);
        });

        view()->composer('layouts.admin.sidebar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $userRoles = auth()->user()->roles;
            $userPermissions = $userRoles->map->permissions->flatten();
            $sidebar = collect([
                'Property' => ['icon' => 'bank', 'submodules' => ['property', 'unit', 'utilities']],
                'Leasing' => ['icon' => 'key','submodules' => ['lease']],
                'Accounting' => ['icon' => 'cash-usd', 'submodules' => ['chartofaccounts']],
                'Communication' => ['icon' => 'email-open', 'submodules' => ['',]],
                'Maintenance' => ['icon' => 'broom', 'submodules' => ['',]],
                'Tasks' => ['icon' => 'timetable', 'submodules' => ['',]],
                'Files' => ['icon' => 'file-multiple', 'submodules' => ['',]],
                'Settings' => ['icon' => 'settings', 'submodules' => ['setting']],
                'User' => ['icon' => 'account-circle-outline', 'submodules' => ['user', 'role', 'permission']],
            ]);
          //  $notifications = $user->notifications;
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user,
                'sidebar' => $sidebar,
                'userPermissions' =>$userPermissions]);
        });

        ////////////////// EMAIL //////////////////////////
        view()->composer('email.template', function($view) {
            $sitesettings = SettingSite::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
    }
}
