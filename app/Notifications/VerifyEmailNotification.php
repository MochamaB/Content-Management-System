<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\Lang;

class VerifyEmailNotification extends BaseVerifyEmail
{
    use Queueable;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
   

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = 'Verify Your Email Address';
        $this->heading = 'Verify Email';
        $this->linkmessage = 'Verify Email Address';
        $this->data = [
            'line1' => 'Please click the button below to verify your email address.',
            'line2' => 'If you did not create an account, no further action is required.',
        ];
        
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
        // Generate the verification URL using parent's method
        $verificationUrl = $this->verificationUrl($notifiable);

        // Return the custom template
        return (new MailMessage)
        ->view('email.template', [
            'user' => $notifiable,
            'heading' => $this->heading,
            'data' => array_merge($this->data, ['action' => $verificationUrl]),
            'linkmessage' => $this->linkmessage,
        ])
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
            'user_id' => $notifiable->id,
            'phonenumber' => $notifiable->phonenumber,
            'user_email' =>  $notifiable->email,
            'subject' => $this->subject ?? null,
            'heading' => $this->heading ?? null,
            'linkmessage' => $this->linkmessage ?? null,
            'data' => $this->data ?? null,
            'channels' => $this->via($notifiable),
        ];
    }
}
