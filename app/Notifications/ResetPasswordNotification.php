<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPasswordNotification extends BaseResetPassword
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
    public function __construct(string $token)
    {
        parent::__construct($token); // Call the parent constructor

        $this->subject = 'Reset Your Password';
        $this->heading = 'Reset Password';
        $this->linkmessage = 'Reset Password';
        $this->data = [
            'line1' => 'Please click the button below to reset your password.',
            'line2' => 'This password reset link will expire in ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes.',
            'line3' => 'Ignore this email if you did not request a password reset',
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
        $resetUrl = $this->resetUrl($notifiable);
         // Return the custom template
         return (new MailMessage)
         ->view('email.template', [
             'user' => $notifiable,
             'heading' => $this->heading,
             'data' => array_merge($this->data, ['action' => $resetUrl]),
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
