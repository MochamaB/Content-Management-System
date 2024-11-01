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
        // Check if the job is of type SendTextNotification
        if ($event->notification instanceof \App\Notifications\SendTextNotification) {
            try {
                // The notification ID is available in the database notification
                $notificationId = $event->notification->id;
                $results = $event->notification->getSendResults();

                if ($results['success']) {
                    Notification::where('id', $notificationId)->update(['status' => 'sent']);
                    $this->smsService->finalizeCreditTransaction($results['success_count'], $results['fail_count']);
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
