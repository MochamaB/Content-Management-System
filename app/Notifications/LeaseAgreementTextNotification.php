<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Website;
use Illuminate\Support\Facades\Log;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class LeaseAgreementTextNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $user;
    protected $lease;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
    protected $company;
    protected $smsContent; // Declare a class property to hold the SMS content
    public $results;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->company = Website::pluck('company_name')->first();
        $this->smsContent = $this->generateSmsContent();
        $this->results = ['success' => false]; // Default to failed
    }

    public function generateSmsContent()
    {
        $link = url('/dashboard/'); // link

        return  "Hi {$this->user->firstname} {$this->user->lastname}, A lease has been assigned to you in {$this->company} property system . 
                     Click to access your account: {$link}";
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
     // SMS Notification using AfricasTalking
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
        return [
            'user_id' => $this->user->id,
            'to' => $this->user->phonenumber,
            'from' => 'System Generated',
            'sms_content' => $this->smsContent, // Include the SMS content here
            'channels' => $this->via($notifiable),
        ];
    }
}
