<?php

namespace App\Listeners;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class NotificationJobFailure
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }


    /**
     * Handle the event.
     *
     * @param  \Illuminate\Queue\Events\NotificationFailed  $event
     * @return void
     */
    public function handle(NotificationFailed $event)
    {
        // Define an array of notification types that need to be handled
        $notificationTypes = [
            \App\Notifications\SendTextNotification::class,
            \App\Notifications\TicketTextNotification::class, // Add other notification types as needed
            \App\Notifications\InvoiceGeneratedTextNotification::class,
            \App\Notifications\LeaseAgreementTextNotification::class,
            \App\Notifications\PaymentTextNotification::class,
            \App\Notifications\UserCreatedTextNotification::class,
        ];

        // Check if the notification is an instance of any specified types
        if (in_array(get_class($event->notification), $notificationTypes)) {
            try {
                // The notification ID is available in the database notification
                $notificationId = $event->notification->id;
                if ($notificationId) {
                    Notification::where('id', $notificationId)->update(['status' => 'failed']);
                    $this->smsService->releaseAllCredits();

                    Log::error('Notification marked as failed', [
                        'id' => $notificationId,
                        'type' => get_class($event->notification),
                        'error' => $event->exception->getMessage()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to update notification status', [
                    'error' => $e->getMessage(),
                    'notification_type' => get_class($event->notification)
                ]);
            }
        }
    }
}
