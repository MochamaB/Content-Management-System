<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WebsiteSetting;
class LeaseAgreementNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $lease;
    protected $subject;
    protected $heading;
    protected $linkmessage;
    protected $data;
    protected $company;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->company = WebsiteSetting::pluck('company_name')->first();
        $this->subject = 'New Lease Agreement created';
        $this->heading = 'Welcome! Your Lease is ready';
        $this->linkmessage = 'Go To Site';
        $this->data = ([
            "line 1" => "Welcome to the ".$this->company." property management system",
            "line 2" => "A new lease has been created for you so now you can manage and view all property data from the comfort of your computer",
            "line 3" => "By receiving this email in it means you have agreeed to all the terms and conditions in your lease agreement and signed off on it",
            "line 4" => "The Default password is property123",
            "line 5" => "To view and manage your units, you can login to our client area here:",
            "action" => "/dashboard",
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
        return (new MailMessage)->view(
            'email.template',
            ['user' => $this->user,'data'=> $this->data,'linkmessage' => $this->linkmessage,'heading' =>$this->heading]
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
