<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketWorkOrderNotification extends Notification 
//implements ShouldQueue
{
    use Queueable;
    protected $users;
    protected $ticket;
    protected $loggeduser;
    protected $workOrder;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
    protected $ticketno;
    protected $name;
    protected $role;
    protected $note;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($users,$ticket,$workOrder)
    {
        $this->users = $users;
        $this->ticket = $ticket;
        $this->subject = 'Ticket Work Order';
        $this->heading = 'Your ticket has been updated';
        $this->linkmessage = 'Check Ticket:';
        $this->ticketno = 'Ticket Number:'.$this->ticket->id;
    
        $this->note = $workOrder->notes;
        $this->data = ([
            "line 1" => "Your Ticket Number ".$this->ticketno." Has been updated with message",
            "line 2" => $this->note,
            "line 3" => "If you have any further questions or concerns, please let us know. We are available round-the-clock and always happy to help.",
            "line 4" => "To view the progress of the ticket, Click here",
            'action' => 'ticket/' . $this->ticket->id,
            "actiondata" => "Go To Site",
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
        return ['mail', 'database'];
    
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        foreach ($this->users as $user) {
            return (new MailMessage)->view(
                'email.template',
                ['user' => $user,
                'data'=> $this->data,
                'linkmessage' => $this->linkmessage,
                'heading' =>$this->heading]
            )
        ->subject($this->subject);
            }
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        foreach ($this->users as $user) {
        return [
            'user_id' => $user->id,
            'phonenumber' => $user->phonenumber,
            'user_email' => $user->email,
            'subject' => $this->subject ?? null,
            'heading' => $this->heading ?? null,
            'linkmessage' => $this->linkmessage ?? null,
            'data' => $this->data ?? null,
            'channels' => $this->via($notifiable),
        ];
    }
    }
}
