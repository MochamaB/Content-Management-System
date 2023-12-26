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
use Illuminate\Support\Facades\View;
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
            $currentUrl = url()->current();
            $sitesettings = WebsiteSetting::first();
            $view->with([
                'routeName' => $routeName,
                'routeParts' => $routeParts,
                'urlParts' => $urlParts,
                'currentUrl'=> $currentUrl,
                'sitesettings'=> $sitesettings,
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
                'Website' => ['icon' => 'web', 'submodules' => [
                                                        'websitesetting'=> ['display' => 'Site Information'],
                                                        'slider'=> ['display' => 'Picture Sliders'],
                                                        'testimonials'=> ['display' => 'Client Testimonials'],
                                                        'amenity'=> ['display' => 'Property Amenities']
                                                        ]],

                'Property' => ['icon' => 'bank', 'submodules' => [
                                                        'property'=> ['display' => 'Property / Company'], 
                                                        'unit'=> ['display' => 'Units'],
                                                        'utility'=> ['display' => 'Utilities']]],

                'Leasing' => ['icon' => 'key','submodules' => [
                                                        'lease'=> ['display' => 'Leases'],
                                                        'invoice'=> ['display' => 'Invoices'],]],

                'Accounting' => ['icon' => 'cash-usd', 'submodules' => [
                                                        'chartofaccount'=> ['display' => 'Chart Of Accounts'],
                                                        'payment-type'=> ['display' => 'Payment Types'],]],

                'Communication' => ['icon' => 'email-open', 'submodules' => ['',]],

                'Maintenance' => ['icon' => 'broom', 'submodules' => ['',]],

                'Tasks' => ['icon' => 'timetable', 'submodules' => [
                                                                    'task'=> ['display' => 'System Tasks'],]],

                'Files' => ['icon' => 'file-multiple', 'submodules' => ['',]],

                'Settings' => ['icon' => 'settings', 'submodules' => [
                                                'setting'=> ['display' => 'Application Settings']]],
                'User' => ['icon' => 'account-circle-outline', 'submodules' => [
                                                            'user'=> ['display' => 'Manage Users'],
                                                            'role'=> ['display' => 'User Roles'], 
                                                            'permission'=> ['display' => 'System Permissions']]],
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
        view()->composer('admin.lease.document_view', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = WebsiteSetting::first();
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user
                ,'sitesettings' => $sitesettings]);
        });
    }
}
