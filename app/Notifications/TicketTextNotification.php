<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
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

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->subject = 'New Ticket Added';
        $this->heading = 'Your ticket has been sent';
        $this->linkmessage = 'Check Ticket:';
        $this->ticketno = 'Ticket Number:'.$this->ticket->id;
        $this->data = ([
            "line 1" => "This is just a quick note to inform you that we received your ".$this->ticket->category." and have already started working on resolving your issue.",
            "line 2" => "Your Ticket Number is ".$this->ticketno,
            "line 3" => "If you have any further questions or concerns, please let us know. We are available round-the-clock and always happy to help.",
            "line 4" => "To view the progress of the ticket, Click here",
            'action' => 'ticket/' . $this->ticket->id,
            "line 5" => "",
        ]);
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
        $ticketRef = $this->ticket->id;
        $propertyName = $this->ticket->property->property_name;
        $unitNumber = $this->ticket->unit->unit_number ?? null;
        $ticketcategory = $this->ticket->category;
        $ticketLink = url('/ticket/' . $this->ticket->id); // Replace with actual payment link
        $smsContent = "A new {$ticketcategory} ticket Id: {$ticketRef} for {$propertyName}, Unit {$unitNumber}, has beeb added. Click here for progress: {$paymentLink}";    
        return (new AfricasTalkingMessage())
        ->content($smsContent);
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
            'phonenumber' => $this->user->phonenumber,
            'user_email' => $this->user->email,
            'subject' => $this->subject ?? null,
            'heading' => $this->heading ?? null,
            'linkmessage' => $this->linkmessage ?? null,
            'data' => $this->data ?? null,
            'channels' => $this->via($notifiable),
        ];
    }
}
