<?php

namespace App\Providers;

use App\Models\WebsiteSetting;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\Lease;
use App\Models\Utility;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Scopes\UnitAccessScope;
use App\Scopes\PropertyAccessScope;
use App\Scopes\UtilityAccessScope;
use App\Scopes\UserAccessScope;

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
          // Apply the UserAccessScope to specific models
        Lease::addGlobalScope(new UserAccessScope);

        Property::addGlobalScope(new PropertyAccessScope);
        Utility::addGlobalScope(new UtilityAccessScope);
        
      Unit::addGlobalScope(new UnitAccessScope);
    // Invoice::addGlobalScope(new UnitAccessScope);
    //   User::addGlobalScope(new UnitAccessScope);


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
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('layouts.client.footer', function($view) {
            $sitesettings = WebsiteSetting::first();
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
            $sitesettings = WebsiteSetting::first();
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user
                ,'sitesettings' => $sitesettings]);
        });

        view()->composer('layouts.admin.adminnavbar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = WebsiteSetting::first();
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
                'Property' => ['icon' => 'bank', 'submodules' => ['property', 'unit', 'utility']],
                'Leasing' => ['icon' => 'key','submodules' => ['lease']],
                'Accounting' => ['icon' => 'cash-usd', 'submodules' => ['chartofaccount']],
                'Communication' => ['icon' => 'email-open', 'submodules' => ['',]],
                'Maintenance' => ['icon' => 'broom', 'submodules' => ['',]],
                'Tasks' => ['icon' => 'timetable', 'submodules' => ['',]],
                'Files' => ['icon' => 'file-multiple', 'submodules' => ['',]],
                'Settings' => ['icon' => 'settings', 'submodules' => ['setting','websitesetting']],
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
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('email.emailtemplate', function($view) {
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
    }
}
