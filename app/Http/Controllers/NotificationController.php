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
use Illuminate\Support\Facades\View;
use ReflectionClass;

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

        $user = Auth::user();
        // Check if the user has permission to view all notifications
        if (Gate::allows('view-all', $user)) {
           // $notifications = Notification::all();
            $mailNotifications = Notification::whereJsonContains('data->channels', ['mail'])
            ->orderBy('created_at', 'desc')->get();

            $unreadNotifications = $mailNotifications->where('read_at', null);
            $readNotifications = $mailNotifications->where('read_at', '!=', null);
       
        } else {
             // For users with restricted access, filter their notifications
            $unreadNotifications = $user->unreadNotifications->filter(function ($notification) {
                return in_array('mail', json_decode($notification->data, true)['channels']);
            })->sortByDesc('created_at');
            $readNotifications = $user->readNotifications->filter(function ($notification) {
                return in_array('mail', json_decode($notification->data, true)['channels']);
            })->sortByDesc('created_at');
        }

        $inboxNotifications = $unreadNotifications->concat($readNotifications)->map(function ($notification) {
            $data = json_decode($notification->data, true);
            $notificationType = $notification->type;
            
            try {
                $reflection = new ReflectionClass($notificationType);
                $instance = $reflection->newInstanceWithoutConstructor();
                $toMailMethod = $reflection->getMethod('toMail');
                $mailMessage = $toMailMethod->invoke($instance, $notification->notifiable);
                
                if (method_exists($mailMessage, 'view')) {
                    if (isset($data['data']['modelname']) && isset($data['data']['model_id'])) {
                        $modelName = $data['data']['modelname'];
                        $modelId = $data['data']['model_id'];
                        switch ($modelName) {
                            case 'Invoice':
                                $invoice = Invoice::find($modelId);
                                $viewData = [
                                    'invoice' => $invoice,
                                ];
                            case 'Payment':
                                $payment = Payment::find($modelId);
                                $viewData = [
                                    'payment' => $payment,
                                ];
                                break;
                                // Add cases for other models you expect to handle
                            default:
                            
                        }
                    }else{
                    $viewData = [
                        'user' => $notification->notifiable,
                        'data' => $data['data'] ?? [],
                        
                    ];
                }
               
                    $viewName = $mailMessage->view;
                    $renderedView = View::make($viewName, $viewData)->render();
                    $data['body'] = $renderedView;
                } else {
                    $data['body'] = 'Email content not available';
                }
            } catch (\Exception $e) {
                $data['body'] = 'Error rendering email content: ' . $e->getMessage();
            }
    
            $notification->data = json_encode($data);
            return $notification;
        });
    

        
        
        return View('admin.Communication.email', compact('inboxNotifications'));
    }


    public function showEmail($uuid)
    {
       // Retrieve the notification using Laravel's DatabaseNotification model
       $notification = Notification::findOrFail($uuid);
       // Decode the notification data
       $notificationData = json_decode($notification->data, true); // Decoding to an associative array
       // dd($notificationData);
        // Mark as read if it hasn't been read yet
       // if (is_null($notification->read_at)) {
       //     $notification->markAsRead();
     //   }
        // Initialize model object
        $model = null;
        $emailContent = null;
        $templateData = [];
        $templateView = null;
        // Check if modelname and model_id exist in the notification data
        if (isset($notificationData['modelname']) && isset($notificationData['model_id'])) {
            $modelName = $notificationData['modelname'];
            $modelId = $notificationData['model_id'];

            // Dynamically resolve the model based on the model name
            switch ($modelName) {
                case 'Invoice':
                    $model = Invoice::find($modelId);
                    $templateData =  [
                        'model' => $model,  // Email is here
                    ];
                    $templateView = 'email.invoice';
                  //  $emailContent = view("email.invoice", compact('model'))->render();
                    break;
                case 'Payment':
                    $model = Payment::find($modelId);
                    $templateData =  [
                        'model' => $model,  // Email is here
                    ];
                    $templateView = 'email.payment';
                   // $emailContent = view("email.payment", compact('model'))->render();
                    break;
                    // Add cases for other models you expect to handle
                default:
                $templateView = 'admin.Communication.template';
                $templateData =  [
                    'user' => $notificationData['user_email'] ?? null,  // Email is here
                    'data' => $notificationData['data'] ?? [],          // This holds the lines
                    'linkmessage' => $notificationData['linkmessage'] ?? '',
                    'heading' => $notificationData['heading'] ?? ''
                ];
                    break;
            }
        } else {
            $templateView = 'admin.Communication.template';
            $templateData =  [
                'user' => $notificationData['user_email'] ?? null,  // Email is here
                'data' => $notificationData['data'] ?? [],          // This holds the lines
                'linkmessage' => $notificationData['linkmessage'] ?? '',
                'heading' => $notificationData['heading'] ?? ''
            ];
            // For notifications without modelname and model_id, use the default template
        }
         // Mark notification as read
      //  $notification->markAsRead();
        
        // Load email content or view based on the model retrieved
       
      //  return view('admin.Communication.email_details',compact('notificationData','emailContent'));
      // For direct URL access, return the full layout with the email details
    $tabTitles = ['Inbox', 'Sent', 'Drafts', 'Trash'];
    $tabContents = [
        view('admin.Communication.email_details', compact('notificationData', 'templateView','templateData'))->render(),
        'Sent Content',
        'Drafts Content',
        'Trash Content'
    ];
    
    return view('admin.Communication.email', compact('tabTitles', 'tabContents'));
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
