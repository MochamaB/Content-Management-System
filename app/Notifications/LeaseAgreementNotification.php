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
    public function __construct($user)
    {
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
        $heading = 'Welcome! New Lease Created';
        $linkmessage = 'To view and manage your lease details. Login here';
        $data = ([
            "line 1" => "'Welcome. A new lease agreement for you has been created for you",
            "line 2" => "This means that all information concerning the unit you gave rented will be available
                        to you using the Tenants portal",
            "line 3" => "If its your first login,The Default password is property123",
            "action" => "/lease",
            "line 4" => "",
        ]);
        return (new MailMessage)->view(
            'email.template',
            ['user' => $this->user,'data'=> $data,'linkmessage' => $linkmessage,'heading' =>$heading]
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
