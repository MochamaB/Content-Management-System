<?php

namespace App\Notifications;

// Rest of the code goes here


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class PaymentNotification extends Notification
{
  
    protected $payment;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payment,$user)
    {
        $this->payment = $payment;
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
        $paymentpdf = PDF::loadView('email.payment', ['payment' => $this->payment]);
       // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        $referenceno = $this->payment->referenceno;
        $paymentfilename = $referenceno.' '.$this->payment->unit->unit_number.' receipt.pdf';
        $paymentdate = Carbon::parse($this->payment->created)->format('Y-m-d');

        $heading = 'New '.$this->payment->model->invoice_type.' Payment';
        $linkmessage = 'To view all your Receipts. Login here';
        $data = ([
            "line 1" => "Please find attached Invoice Ref Number  ".$referenceno,
            "line 2" => $this->payment->model->invoice_type." Charge was paid on ".$paymentdate,
            "line 3" => "Login to the portal to get your account statement",
            "action" => "payment/".$this->payment->id,
            "line 4" => "",
        ]);
        return (new MailMessage)
            ->view(
                'email.template',
                ['user' => $this->user,'data'=> $data,'linkmessage' => $linkmessage,'heading' =>$heading])
                    ->subject($this->payment->model->invoice_type.' Receipt')
                    ->attachData($paymentpdf->output(), $paymentfilename);
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
