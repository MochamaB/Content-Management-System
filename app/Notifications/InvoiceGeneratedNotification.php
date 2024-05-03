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

class InvoiceGeneratedNotification extends Notification 
//implements ShouldQueue
//implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $user;
    protected $openingBalance;
    protected $subject;
    protected $heading;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $user, $openingBalance)
    {

        $this->invoice = $invoice;
        $this->user = $user;

        $this->openingBalance = $openingBalance;
        $this->subject = $this->invoice->name . ' Invoice ' . \Carbon\Carbon::parse($this->invoice->created_at)->format('d M Y');
        $this->heading =  'New ' . $this->invoice->name . ' Invoice';
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

        $transactions = $this->invoice->getTransactions();
        $groupedInvoiceItems = $this->invoice->getGroupedInvoiceItems();

        // Dump the variables
   // dd($transactions, $groupedInvoiceItems, $this->openingBalance);
        $invoicepdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        $referenceno = $this->invoice->id . "-" . $this->invoice->referenceno;
        $invoicefilename = $this->invoice->type . ' - ' . $referenceno . ' ' . $this->invoice->unit->unit_number . ' invoice.pdf';




        return (new MailMessage)
            ->view(
                'email.statement',
                [
                    'user' => $this->user,
                    'invoice' => $this->invoice,
                    'transactions' => $transactions,
                    'groupedInvoiceItems' => $groupedInvoiceItems,
                    'openingBalance' =>  $this->openingBalance
                ]
            )
            ->subject($this->subject)
            ->attachData($invoicepdf->output(), $invoicefilename);
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
