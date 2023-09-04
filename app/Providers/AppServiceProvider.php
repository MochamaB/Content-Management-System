<?php

namespace App\Providers;

use App\Models\SettingSite;
use App\Models\Slider;
use App\Models\Testimonial;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

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

        ////////////////// EMAIL //////////////////////////
        view()->composer('email.template', function($view) {
            $sitesettings = SettingSite::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
    }
}
