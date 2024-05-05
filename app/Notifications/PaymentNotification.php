<?php

namespace App\Notifications;

// Rest of the code goes here

use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class PaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $payment;
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
    public function __construct($payment,$user)
    {
        $this->payment = $payment;
        $this->user = $user;
        $this->company = WebsiteSetting::pluck('company_name')->first();
        $this->subject = $this->payment->model->name.' Receipt';
        $this->heading =  'New '.$this->payment->model->type.' Payment';
        $this->linkmessage = 'Go To Site';
       
        $paymentdate = Carbon::parse($this->payment->created)->format('Y-m-d');
        $this->data = ([
            "line 1" => "Please find attached Receipt Ref Number  ".$this->payment->referenceno,
            "line 2" => $this->payment->model->type." Charge was paid on ".$paymentdate,
            "line 3" => "Login to the portal to get your account statement",
            "action" => "payment/".$this->payment->id,
            "line 4" => "",
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
        $paymentpdf = PDF::loadView('email.payment', ['payment' => $this->payment]);
        $paymentfilename = $this->payment->referenceno.' '.$this->payment->unit->unit_number.' receipt.pdf';
       // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        return (new MailMessage)
            ->view(
                'email.template',
                ['user' => $this->user,'data'=> $this->data,'linkmessage' => $this->linkmessage,'heading' =>$this->heading])
                    ->subject($this->payment->model->name.' Receipt')
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
            'subject' => $this->subject ?? null,
            'heading' => $this->heading ?? null,
            'linkmessage' => $this->linkmessage ?? null,
            'data' => $this->data ?? null,
            'channels' => $this->via($notifiable),
        ];
    }
}
