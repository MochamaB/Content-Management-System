<?php

namespace App\Listeners;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class NotificationJobSuccess
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
     * @param  \Illuminate\Queue\Events\NotificationSent   $event
     * @return void
     */
    public function handle(NotificationSent $event)
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
                $results = $event->notification->getSendResults();

                if ($results['success']) {
                    Notification::where('id', $notificationId)->update(['status' => 'sent']);
                    $this->smsService->finalizeCreditTransaction(1, 0);
                    Log::info("Notification marked as sent", ['id' => $notificationId]);
                } else {
                    Notification::where('id', $notificationId)->update(['status' => 'failed']);
                    $this->smsService->releaseAllCredits();
                    Log::error("Notification failed to send", ['id' => $notificationId]);
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
