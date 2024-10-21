<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailNotification extends Notification 
//implements ShouldQueue
{
    use Queueable;
    protected $message;
    protected $user;
    protected $loggedUser;
    protected $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $subject, $message, $loggedUser)
    {
        $this->message = $message;
        $this->user = $user;
        $this->loggedUser = $loggedUser;
        $this->subject = $subject;
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
        return (new MailMessage)->view(
            'email.send_template',
            ['user' => $this->user,'customMessage' => $this->message]
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
        $from = $this->loggedUser->firstname.' '.$this->loggedUser->lastname;
        return [
            'user_id' => $this->user->id,
            'phonenumber' => $this->user->phonenumber,
            'user_email' => $this->user->email,
            'subject' => $this->subject ?? null,
            'heading' => $this->heading ?? null,
            'from' => $from ??'System Generated',
            'data' => $this->message ?? null,
            'channels' => $this->via($notifiable),
        ];
    }
}
