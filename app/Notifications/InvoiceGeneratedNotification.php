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

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
//implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $user;
    protected $openingBalance;
    protected $subject;
    protected $heading;
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
        $this->subject = $this->invoice->name . ' Invoice ' . \Carbon\Carbon::parse($this->invoice->created_at)->format('d M Y');
        $this->heading =  'New ' . $this->invoice->name . ' Invoice';
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
        return ['mail', 'database', AfricasTalkingChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $invoicepdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        $referenceno = $this->invoice->referenceno;
        $invoicefilename = $referenceno. ' invoice.pdf';



        return (new MailMessage)
            ->view(
                'email.invoicenotification',
                [
                    'viewContent' => $this->view,
                ]
            )
            ->subject($this->subject)
            ->attachData($invoicepdf->output(), $invoicefilename);
    }

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
    return (new AfricasTalkingMessage())
            ->content("{$invoiceName}Invoice Ref: {$invoiceRef} for {$propertyName}, Unit {$unitNumber} of Amount: \${$amountDue} is due. Click here to pay: {$paymentLink}");
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
