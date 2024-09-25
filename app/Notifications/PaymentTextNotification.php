<?php

namespace App\Notifications;

// Rest of the code goes here

use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class PaymentTextNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $payment;
    protected $view;
    protected $user;
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
    public function __construct($payment, $user, $view)
    {
        $this->payment = $payment;
        $this->user = $user;
        $this->company = Website::pluck('company_name')->first();
        $this->subject = $this->payment->model->name . ' Receipt' . \Carbon\Carbon::parse($this->payment->created_at)->format('d M Y');
        $this->heading =  'New ' . $this->payment->model->type . ' Payment';
        $this->linkmessage = 'Go To Site';
        $this->view = $view;

        $paymentdate = Carbon::parse($this->payment->created)->format('Y-m-d');
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', AfricasTalkingChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toAfricasTalking($notifiable)
    {
        $paymentRef = $this->payment->referenceno;
        $propertyName = $this->payment->property->property_name;
        $unitNumber = $this->payment->unit->unit_number;
        $paymentName = class_basename($this->payment->model);
        $amountPaid = $this->payment->totalamount;
        $paymentLink = url('/payment/' . $this->payment->id); // Replace with actual payment link
        $smsContent = "Payment received for {$paymentName} Ref: {$paymentRef} for {$propertyName}, Unit {$unitNumber}, Amount Paid: KSH{$amountPaid} Click here for receipt: {$paymentLink}";    
        return (new AfricasTalkingMessage())
        ->content($smsContent);
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