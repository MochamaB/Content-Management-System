<?php

namespace App\Notifications;

use App\Scopes\PropertyAccessScope;
use App\Scopes\UnitAccessScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Services\TableViewDataService;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class InvoiceGeneratedTextNotification extends Notification implements ShouldQueue
//implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $user;
    protected $view;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $user, $view)
    {

        $this->invoice = $invoice;
        $this->user = $user;
        $this->view = $view; 
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
 
    // send SMS ///
    public function toAfricasTalking($notifiable)
    {
        // Assuming $this->invoice contains the invoice details
    $invoiceRef = $this->invoice->referenceno;
    $propertyName = $this->invoice->property->property_name;
    $unitNumber = $this->invoice->unit->unit_number;
    $invoiceName = $this->invoice->name;
    $amountDue = $this->invoice->totalamount;
    $paymentLink = url('/invoice/' . $this->invoice->id); // Replace with actual payment link
    $smsContent = "{$invoiceName} Invoice Ref: {$invoiceRef} for {$propertyName}, Unit {$unitNumber}, Amount Due: KSH{$amountDue} Click here to pay: {$paymentLink}";    
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
            'linkmessage' => null,
            'data' => null,
            'channels' => $this->via($notifiable),
        ];
    }
}
