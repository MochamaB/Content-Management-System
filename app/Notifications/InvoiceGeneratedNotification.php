<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Services\TableViewDataService;
use Carbon\Carbon;

class InvoiceGeneratedNotification extends Notification
{
    use Queueable;
    protected $invoice;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice,$user)
    {
        $this->invoice = $invoice;
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
        $invoicepdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
       // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        $referenceno = $this->invoice->id."-".$this->invoice->referenceno;
        $invoicefilename = $this->invoice->invoice_type.' - '.$referenceno.' '.$this->invoice->unit->unit_number.' invoice.pdf';
        $duedate = Carbon::parse($this->invoice->duedate)->format('Y-m-d');

        $heading = 'New '.$this->invoice->invoice_type.' Invoice';
        $linkmessage = 'To view all your invoices. Login here';
        $data = ([
            "line 1" => "Please find attached Invoice Ref Number  ".$referenceno,
            "line 2" => $this->invoice->invoice_type." Charge due on ".$duedate,
            "line 3" => "Login to the portal to get your account statement",
            "action" => "invoice/".$this->invoice->id,
            "line 4" => "",
        ]);
        return (new MailMessage)
            ->view(
                'email.template',
                ['user' => $this->user,'data'=> $data,'linkmessage' => $linkmessage,'heading' =>$heading])
                    ->subject($this->invoice->invoice_type.' Invoice')
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
            
        ];
    }
}
