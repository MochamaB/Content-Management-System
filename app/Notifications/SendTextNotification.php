<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;
use NotificationChannels\AfricasTalking\Exceptions\CouldNotSendNotification;

class SendTextNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $user;
    protected $loggedUser;
    protected $smsContent; 
    public $results;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $message, $loggedUser)
    {
        $this->message = $message;
        $this->user = $user;
        $this->loggedUser = $loggedUser;
        $this->smsContent = $this->generateSmsContent();
        $this->results = ['success' => false]; // Default to failed
    }

    public function generateSmsContent()
    {
        $message = $this->message;
        
        return  "{$message}";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', AfricasTalkingChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toAfricasTalking($notifiable)
    {
        try {
            $message = new AfricasTalkingMessage();
            $message->content($this->smsContent);
            // If no exception occurs, mark as success
            $this->markAsSuccess();
            return $message;
        }catch (\Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            // Failures will be caught in NotificationFailed
            throw $e; // Rethrow to trigger NotificationFailed event
        }
    }
    public function markAsSuccess()
    {
        $this->results['success'] = true; // Call this when the API response confirms success
    }

    public function getSendResults()
    {
        return $this->results;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $from = $this->loggedUser->firstname.' '.$this->loggedUser->lastname;
        return [
            'user_id' => $this->user->id,
            'to' => $this->user->phonenumber,
            'from' => $from ??'System Generated',
            'sms_content' => $this->smsContent, // Include the SMS content here
            'channels' => $this->via($notifiable),
        ];
    }

    
}
