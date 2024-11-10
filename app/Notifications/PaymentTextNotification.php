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
use Illuminate\Support\Facades\Log;
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
    protected $model;
    protected $smsContent; // Declare a class property to hold the SMS content
    public $results;

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
        // Set the model name using class_basename
        $this->model = class_basename($payment); // This will return the model's class name, e.g., "Invoice"
        $this->smsContent = $this->generateSmsContent();
        $this->results = ['success' => false]; // Default to failed

        $paymentdate = Carbon::parse($this->payment->created)->format('Y-m-d');
    }

    public function generateSmsContent()
    {
        $paymentRef = $this->payment->referenceno;
        $propertyName = $this->payment->property->property_name;
        $unitNumber = $this->payment->unit->unit_number;
        $paymentName = class_basename($this->payment->model);
        $amountPaid = $this->payment->totalamount;
        $paymentLink = url('/payment/' . $this->payment->id); // Replace with actual payment link
        
        return "Payment received for {$paymentName} Ref: {$paymentRef} for {$propertyName}, Unit {$unitNumber}, Amount Paid: KSH{$amountPaid} Click here for receipt: {$paymentLink}";    
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
        try {
            $message = new AfricasTalkingMessage();
            $message->content($this->smsContent);
            // If no exception occurs, mark as success
            $this->markAsSuccess();
            return $message;
        }catch (\Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            // Failures will be caught in NotificationFailed
            throw $e; // Rethrow to trigger NotificationFailed event
        }
    }

    public function markAsSuccess()
    {
        $this->results['success'] = true; // Call this when the API response confirms success
    }

    public function getSendResults()
    {
        return $this->results;
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
            'modelname' => $this->model, // Use the model name set in the constructor
            'model_id' => $this->payment->id ?? null, // Assuming invoice ID is the model ID
            'to' => $this->user->phonenumber,
            'from' => 'System Generated',
            'sms_content' => $this->smsContent, // Include the SMS content here
            'channels' => $this->via($notifiable),
        ];
    }
}
