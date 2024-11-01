<?php

namespace App\Providers;

use App\Events\AssignUserToUnit;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserCreate;
use App\Listeners\AssignUserToUnitListener;
use App\Listeners\SendWelcomeEmailNotification;
use App\Listeners\NotificationJobSuccess;
use App\Listeners\NotificationJobFailure;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreate::class => [
            SendWelcomeEmailNotification::class,
        ],AssignUserToUnit::class => [
            AssignUserToUnitListener::class,
        ],
        NotificationSent::class => [
            NotificationJobSuccess::class,
        ],
        NotificationFailed::class => [
            NotificationJobFailure::class,
        ],
        
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
