<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminTicketNotification extends Notification
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
        $this->subject = 'New Ticket Created';
        $this->heading = 'New Ticket Created';
        $this->linkmessage = 'Check Ticket:';
        $this->ticketno = 'Ticket Number:'.$this->ticket->id;
        $this->data = ([
            "line 1" => "A new ticket has been created in the system.",
            "line 2" => "The Ticket Number is ".$this->ticketno,
            "line 3" => "To view the details and progress of the ticket, Click here.",
            "action" => "/ticket/{{$this->ticket->id}}",
            "actiondata" => "Go To Site",
            "line 4" => "",
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->view(
            'email.template',
            ['user' => $this->user,
            'data'=> $this->data,
            'linkmessage' => $this->linkmessage,
            'heading' =>$this->heading]
        )
        ->subject($this->subject);
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
