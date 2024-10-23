<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Website;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\Lease;
use App\Models\MeterReading;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Deposit;
use App\Models\Utility;
use App\Models\User;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\Unitcharge;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UnitRepository;
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
use App\Scopes\TicketAccessScope;
use App\Scopes\UserScope;
use App\Services\FilterService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InvoiceRepository::class, InvoiceRepository::class);
        $this->app->bind(PaymentRepository::class, PaymentRepository::class);
        $this->app->bind(UnitRepository::class, UnitRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

      //  URL::forceScheme('https');

        Schema::defaultStringLength(191);
        // Apply the UserAccessScope to specific models
        Lease::addGlobalScope(new UserAccessScope);
        Invoice::addGlobalScope(new UserAccessScope);
        MeterReading::addGlobalScope(new UserAccessScope);
        Unitcharge::addGlobalScope(new UserAccessScope);
        Deposit::addGlobalScope(new UserAccessScope);
        Payment::addGlobalScope(new UserAccessScope);

        Unit::addGlobalScope(new UnitAccessScope);

        // Invoice::addGlobalScope(new ApplyFilterScope);

        //   User::addGlobalScope(new UserScope);
        Property::addGlobalScope(new PropertyAccessScope);
        Ticket::addGlobalScope(new TicketAccessScope);

        Utility::addGlobalScope(new UtilityAccessScope);

        /// Format all amounts to thousands
        Blade::directive('currency', function ($expression) {
            return "<?= number_format($expression, 0, '.', ','); ?>";
        });
        //Format all dates to YYYY/MMM/DD
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo ($expression) ? date('Y-m-d', strtotime($expression)) : ''; ?>";
        });

      // Query Website Settings once
      
      if (Schema::hasTable('website_settings')) {
        // Fetch the website settings once and share with all views
        $websitesettings = Website::first();
      
        /////////// GLOBAL VIEW COMPOSERS
        view()->composer('*', function ($view)  use ($websitesettings) {
            $routeName = Route::currentRouteName();
            $routeParts = explode('.', $routeName);
            $urlParts = explode('/', url()->current());
            $currentUrl = url()->current();

                    // Only pass the authenticated user if it's not already set in the view
            if (!$view->offsetExists('user')) {
                $user = auth()->user();
                $view->with('user', $user);
            }
            $view->with([
                'routeName' => $routeName,
                'routeParts' => $routeParts,
                'urlParts' => $urlParts,
                'currentUrl' => $currentUrl,
               'sitesettings' => $websitesettings ? $websitesettings : (object)[
                    'site_currency' => 'KSH', // Set default values
                    'company_name' => 'Default Company',
                    'initials' => 'BP',
                     'logo' => 'noimage.jpg',
                ],
            ]);
        });
      

        //////////////  FRONT END/////////////
       
    }

        view()->composer('client.slider', function ($view) {
            $slider = Slider::all();
            $view->with(['slider' => $slider]);
        });


        view()->composer('client.testimonial', function ($view) {
            $testimonial = Testimonial::all();
            $view->with(['testimonial' => $testimonial]);
        });

        view()->composer('layouts.admin.master-filter', function ($view) {
            $defaultfilter = (new FilterService())->getDefaultFilters();
            $view->with('defaultfilter', $defaultfilter);
        });

        /////////ADMIN//////////////////////////////

        

        view()->composer('layouts.admin.adminnavbar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
          
            if (Gate::allows('view-all', $user) || Gate::allows('admin', $user)) {
                $notifications = Notification::all();
                $unreadNotifications = $notifications->where('read_at', null);
            } else {
                $unreadNotifications = $user->unreadNotifications ?? collect();
              //  $unreadNotifications = $unreadNotifications->where('read_at', null);
            }
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user, 
                'unreadNotifications' => $unreadNotifications
            ]);
        });

        view()->composer('layouts.admin.sidebar', function ($view) {
            // Get the authenticated user, assuming you are using the default 'auth' guard
            $user = auth()->user();
            $userRoles = auth()->user()->roles;
            $userPermissions = $userRoles->map->permissions->flatten();

            $sidebar = collect([
                ////Main Menu Items
                'Main' => [
                'Property' => ['icon' => 'bank', 'submodules' => [
                    'property' => ['display' => 'Property / Company'],
                    'unit' => ['display' => 'Units'],
                    'utility' => ['display' => 'Utilities'],
                    'tenant' => ['display' => 'Tenants']
                ]],

                'Leasing' => ['icon' => 'key', 'submodules' => [
                    'lease' => ['display' => 'Leases'],
                    'invoice' => ['display' => 'Invoices'],
                    'unitcharge' => ['display' => 'All Utility Charges'],
                    'payment' => ['display' => 'Payments'],
                    'meter-reading' => ['display' => 'Meter Readings'],
                ]],

                'Maintenance' => ['icon' => 'broom', 'submodules' => [
                    'vendor-category' => ['display' => 'Vendor Categories'],
                    'vendors' => ['display' => 'Vendors'],
                    'ticket' => ['display' => 'All Tickets'],
                ]],

                'Accounting' => ['icon' => 'cash-usd', 'submodules' => [
                    'expense' => ['display' => 'Bills & Expenses'],
                    'deposit' => ['display' => 'Deposits'],
                    'transaction' => ['display' => 'Financials'],
                    'general-ledger' => ['display' => 'General Ledger'],
                    'income-statement' => ['display' => 'Profit and Loss'],
                    'chartofaccount' => ['display' => 'Chart Of Accounts'],
                    'payment-method' => ['display' => 'Payment methods'],
                    'tax' => ['display' => 'Taxes'],
                    'transaction-type' => ['display' => 'Transaction Types'],
                ]],

                'Communication' => ['icon' => 'email-open', 'submodules' => [
                    'notification' => ['display' => 'Notification Center'],
                    'email' => ['display' => 'Emails'],
                    'textmessage' => ['display' => 'Text Messages'],
                ]],
                'Files' => ['icon' => 'file-multiple', 'submodules' => [
                    'media' => ['display' => 'All Files'],
                ]],
                'Reports' => ['icon' => 'chart-line', 'submodules' => [
                    'report' => ['display' => 'All Reports'],
                ]],
                


               
            ],
            //// Secondary Menu Items
            'Secondary' => [

                'Tasks' => ['icon' => 'timetable', 'submodules' => [
                    'task' => ['display' => 'System Tasks'],
                ]],
                'Website' => ['icon' => 'web', 'submodules' => [
                    'Website' => ['display' => 'Site Information'],
                    'slider' => ['display' => 'Picture Sliders'],
                    'testimonial' => ['display' => 'Client Testimonials'],
                    'amenity' => ['display' => 'Property Amenities'],
                    'propertytype' => ['display' => 'Property Categories']
                ]],
                

                'Settings' => ['icon' => 'settings', 'submodules' => [
                    'setting' => ['display' => 'Application Settings'],
                    'system-setting' => ['display' => 'System Configurations']
                ]],
                ],
            'Other' => [
                    'User' => ['icon' => 'account-circle-outline', 'submodules' => [
                    'user' => ['display' => 'Manage Users'],
                    'role' => ['display' => 'User Roles'],
                    'permission' => ['display' => 'System Permissions']
                ]],

            ]
            ]);
            //  $notifications = $user->notifications;
            // Pass the authenticated user data to the 'layouts.admin' view
            $view->with([
                'user' => $user,
                'sidebar' => $sidebar,
                'userPermissions' => $userPermissions
            ]);
        });

        
    }
}
