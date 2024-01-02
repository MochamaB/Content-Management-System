<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;
    protected $user;

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
        $heading = 'Welcome! Your Account is ready';
        $linkmessage = 'To view and manage your units, you can login to our client area here:';
        $data = ([
            "line 1" => "Welcome to the property management system",
            "line 2" => "Manage and view all property data from the comfort of your computer",
            "line 3" => "The Default password is property123",
            "action" => "/dashboard",
            "line 4" => "",
        ]);
        return (new MailMessage)->view(
            'email.template',
            ['user' => $this->user,'data'=> $data,'linkmessage' => $linkmessage,'heading' =>$heading]
        )
        ->subject('New User Created');
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
