<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;


//class TicketNotification extends Notification implements ShouldQueue
class TicketTextNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $user;
    protected $ticket;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
    protected $ticketno;
    protected $smsContent; // Declare a class property to hold the SMS content
    public $results;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->smsContent = $this->generateSmsContent();
        $this->results = ['success' => false]; // Default to failed
       
    }

    public function generateSmsContent()
    {
        $ticketRef = $this->ticket->id;
        $propertyName = $this->ticket->property->property_name;
        $unitNumber = $this->ticket->unit->unit_number ?? null;
        $ticketcategory = $this->ticket->category;
        $ticketLink = url('/ticket/' . $this->ticket->id);

        return  "A new {$ticketcategory} ticket Id: {$ticketRef} for {$propertyName}, Unit {$unitNumber}, has been added. Click here for progress: {$ticketLink}";
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
        Log::info('TicketText toArray called', [
            'user_id' => $this->user->id,
            'to' => $this->user->phonenumber,
            'sms_content' => $this->smsContent,
        ]);
        return [
            'user_id' => $this->user->id,
            'to' => $this->user->phonenumber,
            'from' => 'System Generated',
            'sms_content' => $this->smsContent, // Include the SMS content here
            'channels' => $this->via($notifiable),
        ];
    }
}
