<?php

namespace App\Notifications;

use App\Scopes\PropertyAccessScope;
use App\Scopes\UnitAccessScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


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
    protected $model;
    protected $reminder;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $user, $view,$reminder = null)
    {

        $this->invoice = $invoice;
        $this->user = $user;
        $this->reminder = $reminder;
        // Adjust subject with "Reminder" only if $reminder is true
        $this->subject = ($this->reminder ? 'Reminder: ' : '') . $this->invoice->name . ' Invoice ' . \Carbon\Carbon::parse($this->invoice->created_at)->format('d M Y');
        $this->heading =  'New ' . $this->invoice->name . ' Invoice';
        $this->view = $view; 
        // Set the model name using class_basename
        $this->model = class_basename($invoice); // This will return the model's class name, e.g., "Invoice"
       
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
            'modelname' => $this->model, // Use the model name set in the constructor
            'model_id' => $this->invoice->id ?? null, // Assuming invoice ID is the model ID
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
