<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\WebsiteSetting;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\Lease;
use App\Models\MeterReading;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Paymentvoucher;
use App\Models\Utility;
use App\Models\User;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\Unitcharge;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use App\Scopes\UnitAccessScope;
use App\Scopes\PropertyAccessScope;
use App\Scopes\UtilityAccessScope;
use App\Scopes\UserAccessScope;
use App\Scopes\ApplyFilterScope;
use App\Scopes\UserScope;
use App\Services\FilterService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;

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
        Invoice::addGlobalScope(new UserAccessScope);
        MeterReading::addGlobalScope(new UserAccessScope);
        Unitcharge::addGlobalScope(new UserAccessScope);
        Paymentvoucher::addGlobalScope(new UserAccessScope);
        Payment::addGlobalScope(new UserAccessScope);

        Unit::addGlobalScope(new UnitAccessScope);

        // Invoice::addGlobalScope(new ApplyFilterScope);

        //   User::addGlobalScope(new UserScope);
        Property::addGlobalScope(new PropertyAccessScope);
        // Ticket::addGlobalScope(new PropertyAccessScope);

        Utility::addGlobalScope(new UtilityAccessScope);

        Blade::directive('currency', function ($expression) {
            return "<?= number_format($expression, 0, '.', ','); ?>";
        });

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
                'currentUrl' => $currentUrl,
                'sitesettings' => $sitesettings,
            ]);
        });
        view()->composer('layouts.admin.master-filter', function ($view) {
            $defaultfilter = (new FilterService())->getDefaultFilters();
            $view->with('defaultfilter', $defaultfilter);
        });

        //////////////  FRONT END/////////////
        view()->composer('layouts.client.navbar', function ($view) {
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('layouts.client.footer', function ($view) {
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });

        view()->composer('client.slider', function ($view) {
            $slider = Slider::all();
            $view->with(['slider' => $slider]);
        });


        view()->composer('client.testimonial', function ($view) {
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
                'user' => $user, 'sitesettings' => $sitesettings
            ]);
        });

        view()->composer('layouts.admin.adminnavbar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = WebsiteSetting::first();

            if (Gate::allows('view-all', $user)) {
                $notifications = Notification::all();
                $unreadNotifications = $notifications->where('read_at', null);
            } else {
                $unreadNotifications = $user->unreadNotifications;
            }
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user, 'sitesettings' => $sitesettings,
                'unreadNotifications' => $unreadNotifications
            ]);
        });

        view()->composer('layouts.admin.sidebar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $userRoles = auth()->user()->roles;
            $userPermissions = $userRoles->map->permissions->flatten();
            $sidebar = collect([
                'Website' => ['icon' => 'web', 'submodules' => [
                    'websitesetting' => ['display' => 'Site Information'],
                    'slider' => ['display' => 'Picture Sliders'],
                    'testimonial' => ['display' => 'Client Testimonials'],
                    'amenity' => ['display' => 'Property Amenities'],
                    'propertytype' => ['display' => 'Property Categories']
                ]],

                'Property' => ['icon' => 'bank', 'submodules' => [
                    'property' => ['display' => 'Property / Company'],
                    'unit' => ['display' => 'Units'],
                    'utility' => ['display' => 'Utilities'],
                    'tenant' => ['display' => 'Tenants']
                ]],

                'Leasing' => ['icon' => 'key', 'submodules' => [
                    'lease' => ['display' => 'Leases'],
                    'invoice' => ['display' => 'Invoices'],
                    'unitcharge' => ['display' => 'All Charges'],
                    'paymentvoucher' => ['display' => 'Payment Vouchers'],
                    'payment' => ['display' => 'Payments'],
                    'meter-reading' => ['display' => 'Meter Readings'],
                ]],

                'Accounting' => ['icon' => 'cash-usd', 'submodules' => [
                    'general-ledger' => ['display' => 'General Ledger'],
                    'income-statement' => ['display' => 'Profit and Loss'],
                    'chartofaccount' => ['display' => 'Chart Of Accounts'],
                    'payment-method' => ['display' => 'Payment methods'],

                ]],

                'Communication' => ['icon' => 'email-open', 'submodules' => [
                    'notification' => ['display' => 'Notification Center'],
                    'email' => ['display' => 'Emails'],
                    'text' => ['display' => 'Text Messages'],
                ]],

                'Maintenance' => ['icon' => 'broom', 'submodules' => [
                    'vendor-category' => ['display' => 'Vendor Categories'],
                    'vendors' => ['display' => 'Vendors'],
                    'ticket' => ['display' => 'All Tickets'],
                ]],

                'Tasks' => ['icon' => 'timetable', 'submodules' => [
                    'task' => ['display' => 'System Tasks'],
                ]],

                'Files' => ['icon' => 'file-multiple', 'submodules' => [
                    'media' => ['display' => 'All Files'],
                ]],
                'Reports' => ['icon' => 'chart-line', 'submodules' => [
                    'report' => ['display' => 'All Reports'],
                ]],
                'User' => ['icon' => 'account-circle-outline', 'submodules' => [
                    'user' => ['display' => 'Manage Users'],
                    'role' => ['display' => 'User Roles'],
                    'permission' => ['display' => 'System Permissions']
                ]],

                'Settings' => ['icon' => 'settings', 'submodules' => [
                    'setting' => ['display' => 'Application Settings']
                ]],
            ]);
            //  $notifications = $user->notifications;
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user,
                'sidebar' => $sidebar,
                'userPermissions' => $userPermissions
            ]);
        });

        ////////////////// EMAIL //////////////////////////
        view()->composer('email.template', function ($view) {
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('email.emailtemplate', function ($view) {
            $sitesettings = WebsiteSetting::first();
            $view->with(['sitesettings' => $sitesettings]);
        });
        view()->composer('admin.lease.document_view', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $sitesettings = WebsiteSetting::first();
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user, 'sitesettings' => $sitesettings
            ]);
        });
    }
}
