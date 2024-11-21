<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;


class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $user;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,string $token)
    {
        // Call the parent constructor

        $this->user = $user;
        $this->token = $token; // Assign the token
        $this->subject = 'New User Added';
        $this->heading = 'Welcome! Your Account is ready';
        $this->linkmessage = 'Go To Site';
        $this->data = ([
            "line 1" => "Welcome to the property management system.You have been added by your property owner.",
            "line 2" => "Now you can manage and view all property data from the comfort of your computer",
            "line 3" => "Access your portal with your email and create a password here:",
          
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
        $resetUrl = $this->resetUrl($notifiable);
        return (new MailMessage)->view(
            'email.template',
            ['user' => $this->user,
            'data' => array_merge($this->data, ['action' => $resetUrl]),
            'linkmessage' => $this->linkmessage,
            'heading' =>$this->heading]
        )
        ->subject($this->subject);
    }

    protected function resetUrl($notifiable)
{
    return url(route('password.reset', [
        'token' => $this->token,
        'email' => $this->user->email,
    ], false));
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
