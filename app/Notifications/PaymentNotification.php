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
        $this->company = WebsiteSetting::pluck('company_name')->first();
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
        $paymentpdf = PDF::loadView('email.payment', ['payment' => $this->payment]);
        $paymentfilename = $this->payment->referenceno . ' ' . $this->payment->unit->unit_number . ' receipt.pdf';
        // $statementpdf = PDF::loadView('email.invoice', ['invoice' => $this->invoice]);
        // Create a filename using invoice values
        return (new MailMessage)
            ->view(
                'email.invoicenotification',
                [
                    'viewContent' => $this->view,
                ]
            )
            ->subject($this->payment->model->name . ' Receipt')
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
