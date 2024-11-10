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
use Illuminate\Support\Facades\Log;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class InvoiceGeneratedTextNotification extends Notification implements ShouldQueue
//implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $user;
    protected $model;
    protected $reminder;
    protected $smsContent; // Declare a class property to hold the SMS content
    public $results;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $user, $reminder = null)
    {

        $this->invoice = $invoice;
        $this->user = $user;
        $this->reminder = $reminder;
        // Set the model name using class_basename
        $this->model = class_basename($invoice); // This will return the model's class name, e.g., "Invoice"
        $this->smsContent = $this->generateSmsContent();
        $this->results = ['success' => false]; // Default to failed
    }

    public function generateSmsContent()
    {
    $reminder = $this->reminder ? 'Reminder: ' : '';
    $invoiceRef = $this->invoice->referenceno;
    $propertyName = $this->invoice->property->property_name;
    $unitNumber = $this->invoice->unit->unit_number;
    $invoiceName = $this->invoice->name;
    $amountDue = $this->invoice->totalamount;
    $paymentLink = url('/invoice/' . $this->invoice->id); // Replace with actual payment link

    return "{$reminder} {$invoiceName} Invoice Ref: {$invoiceRef} for {$propertyName}, Unit {$unitNumber}, Amount Due: KSH{$amountDue} Click here to pay: {$paymentLink}";    
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
            'model_id' => $this->invoice->id ?? null, // Assuming invoice ID is the model ID
            'to' => $this->user->phonenumber,
            'from' => 'System Generated',
            'sms_content' => $this->smsContent, // Include the SMS content here
            'channels' => $this->via($notifiable),
        ];
    }
}
