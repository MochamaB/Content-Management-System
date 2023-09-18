<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaseAgreementNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $lease;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($lease,$user)
    {
        $this->lease = $lease;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/lease/'.$this->lease->id);
        $subject = 'New Lease Agreement';
        $greetings = 'Welcome. A new lease agreement for you has been created on the';
        $message = 'This means that all information concerning the unit you gave rented will be available
        to you using the site/tenants portal ';
        $linkmessage = 'To Activate the lease. Kindly login and Read through the lease agreement. Then Accept 
        the Terms and Conditions to make the lease Active. Ignore this if you have already done this and the lease is active.';
        $action = 'Go to site';
        $footermessage = 'Go to site';
        

        return (new MailMessage)->view(
            'email.emailtemplate',
            ['user' => $this->user,
            'url'=> $url,
            'subject' => $subject,
            'greetings' => $greetings,
            'message' => $message,
            'linkmessage' => $linkmessage,
            'action' => $action,
            'footermessage' => $footermessage, ]
        );
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
        ];
    }
}
