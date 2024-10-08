<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewsWasPublished;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Notification::class;
        $this->controller = collect([
            '0' => 'notification', // Use a string for the controller name
            '1' => 'Notification',
        ]);

        $this->tableViewDataService = $tableViewDataService;
    }
    public function index(Request $request)
    {
        $filters = $request->except(['tab', '_token', '_method']);
        //  $filterData = $this->filterData($this->model);
        $controller = $this->controller;

        return View('admin.Communication.notification_index', compact('controller'));
    }

    public function email(Request $request)
    {
        $tabTitles = collect([
            'Inbox',
            'Sent',
            'Draft',
            'Outbox',
        ]);

        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $notifications = Notification::all();
            $mailNotifications = Notification::whereJsonContains('data->channels', ['mail'])->get();

            $unreadNotifications = $mailNotifications->where('read_at', null);
            $readNotifications = $mailNotifications->where('read_at', '!=', null);
            //  dd($unreadNotifications);
        } else {
            $unreadNotifications = $user->unreadNotifications->filter(function ($notification) {
                return in_array('mail', json_decode($notification->data, true)['channels']);
            });
            $readNotifications = $user->readNotifications->filter(function ($notification) {
                return in_array('mail', json_decode($notification->data, true)['channels']);
            });
        }
        /*
        $emailview = collect([
          'Roles',
          'Contact Information',
          'Property Access',
          'Review Details',
        ]);
    
        return view('admin.Communication.notification ', [
          'notifications' => $mailNotifications,
          'unreadNotifications' => $unreadNotifications,
          'readNotifications' => $readNotifications,
        ]);
        */
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Inbox') {
                $tabContents[] = View('admin.Communication.email_summary', compact('notifications', 'unreadNotifications', 'readNotifications'))->render();
            } elseif ($title === 'Sent') {
                $tabContents[] = View('admin.Communication.email_summary', compact('notifications', 'unreadNotifications', 'readNotifications'))->render();
            } elseif ($title === 'Drafts') {
                $tabContents[] = View('admin.Communication.email_summary', compact('notifications', 'unreadNotifications', 'readNotifications'))->render();
            } elseif ($title === 'Outbox') {
                $tabContents[] = View('admin.Communication.email_summary', compact('notifications', 'unreadNotifications', 'readNotifications'))->render();
            }
        }
        return View('admin.Communication.email', compact('tabTitles', 'tabContents'));
    }


    public function showEmail($uuid)
    {
       // Retrieve the notification using Laravel's DatabaseNotification model
       $notification = Notification::findOrFail($uuid);
       // Decode the notification data
       $notificationData = $notification->data;
      //  dd($notificationData);

        // Initialize model object
        $model = null;
        $emailContent = null;
        // Check if modelname and model_id exist in the notification data
        if (isset($notificationData['modelname']) && isset($notificationData['model_id'])) {
            $modelName = $notificationData['modelname'];
            $modelId = $notificationData['model_id'];

            // Dynamically resolve the model based on the model name
            switch ($modelName) {
                case 'Invoice':
                    $model = Invoice::find($modelId);
                    $emailContent = view("email.invoice", compact('model'))->render();
                    break;
                case 'Payment':
                    $model = Payment::find($modelId);
                    $emailContent = view("email.payment", compact('model'))->render();
                    break;
                    // Add cases for other models you expect to handle
                default:
                    $model = null; // If no matching model is found, leave model null
                    break;
            }
        } else {
            // For notifications without modelname and model_id, use the default template
            $emailContent = view("email.template", [
                'user' => $notificationData['user'] ?? null,
                'data' => $notificationData['data'] ?? [],
                'linkmessage' => $notificationData['linkmessage'] ?? '',
                'heading' => $notificationData['heading'] ?? ''
            ])->render();
        }
         // Mark notification as read
      //  $notification->markAsRead();
        
        // Load email content or view based on the model retrieved
       
        return view('admin.Communication.email_details ', [
            'notificationData' => $notificationData,
            'emailContent' =>$emailContent
          ]);
    }

    public function text(Request $request)
    {

        return view('admin.Communication.text');
    }

    public function sendText(Request $request)
    {
        // Get the user (test user or any user in your database)

        //  $user = User::find(2);
        // send notification 
        // $user->notify(new NewsWasPublished());
        // Assuming $this->invoice contains the invoice details
        $invoice = Invoice::find(1);

        $invoiceRef = $invoice->referenceno;
        $propertyName = $invoice->property->property_name; // Assuming there's a relationship with Property
        $unitNumber = $invoice->unit->unit_number;         // Assuming there's a relationship with Unit
        $invoiceName = $invoice->name;
        $amountDue = $invoice->totalamount;
        $paymentLink = url('/invoice/' . $invoice->id);  // Replace with actual payment link
        $smsContent = "Invoice Ref: {$invoiceRef} for {$propertyName}, Unit {$unitNumber}, {$invoiceName} Amount Due: KSH{$amountDue}. Click here to pay: {$paymentLink}";

        // Africa's Talking API credentials
        $username = env('AT_USERNAME');
        $apiKey = env('AT_KEY');

        $client = new Client([
            'verify' => false,
            'base_uri' => 'https://api.africastalking.com'
        ]);

        try {
            $response = $client->post('/version1/messaging', [
                'form_params' => [
                    'username' => $username,  // Required field
                    'to' => '+254723710025', // The number you want to test with
                    'message' => $smsContent,
                    'from' => 'AFRICASTKNG', // Your short code
                ],
                'headers' => [
                    'apiKey' => $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            print_r($result);

            return 'SMS sent successfully';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find(1); // get some user

        // send notification 
        $user->notify(new NewsWasPublished());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
