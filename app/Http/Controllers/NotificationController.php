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
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
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

        return View('admin.Communication.notification_index');
    }

    public function email(Request $request)
    {

        $user = Auth::user();
        $perPage = 20;
        $page = Paginator::resolveCurrentPage() ?: 1;
        // Check if the user has permission to view all notifications
        if (Gate::allows('view-all', $user)) {
           // $notifications = Notification::all();
            $mailNotifications = Notification::whereJsonContains('data->channels', ['mail'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

            $unreadNotifications = $mailNotifications->where('read_at', null);
            $readNotifications = $mailNotifications->where('read_at', '!=', null);
       
        } else {
             // For users with restricted access, filter their notifications
             $allNotifications = $user->notifications()
            ->whereJsonContains('data->channels', ['mail'])
            ->orderBy('created_at', 'desc')
            ->get();

            $pagedNotifications = $allNotifications->slice(($page - 1) * $perPage, $perPage);

            $mailNotifications = new LengthAwarePaginator(
                $pagedNotifications,
                $allNotifications->count(),
                $perPage,
                $page,
                ['path' => Paginator::resolveCurrentPath()]
            );
            $unreadNotifications = $pagedNotifications->where('read_at', null);
            $readNotifications = $pagedNotifications->where('read_at', '!=', null);
        }

        $inboxNotifications = $unreadNotifications->concat($readNotifications)->map(function ($notification) {
            $notificationData  = json_decode($notification->data, true);
            $notificationType = $notification->type;
     
            try {
             
                if ($notificationData) {
                   
                     if (isset($notificationData['modelname']) && isset($notificationData['model_id'])) 
                        {
                            $modelName = $notificationData['modelname'];
                            $modelId = $notificationData['model_id'];
                            switch ($modelName) {
                            case 'Invoice':
                                $invoice = Invoice::find($modelId);
                                $viewData['invoice'] = $invoice;
                                $viewName = 'admin.Lease.invoice_contents';
                                break;
                            case 'Payment':
                                $payment = Payment::find($modelId);
                                $viewData['payment'] = $payment;
                                $viewName = 'admin.Lease.payment_contents';
                                break;
                                // Add cases for other models you expect to handle
                            }
                        }else{
                            $viewName = 'admin.Communication.email_template'; // Default view
                            $viewData = [
                                'user' => $notification->notifiable,
                                'data' => $notificationData['data'] ?? [],
                            ];
                        }

                    $renderedView = View::make($viewName, $viewData)->render();
                    $notificationData['body'] = $renderedView;
                } else {
                    $notificationData['body'] = 'Email content not available';
                }
            } catch (\Exception $e) {
                $notificationData['body'] = 'Error rendering email content: ' . $e->getMessage();
            }
    
            $notification->data = json_encode($notificationData);
            return $notification;
        });
    

        
        
        return View('admin.Communication.email', compact('inboxNotifications','mailNotifications'));
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
        $smsContent = "Test Message";
        $username = env('AT_USERNAME');
        $apiKey = env('AT_KEY');
        $from = env('AT_FROM');
        
        
        $client = new Client([
            'verify' => false,
            'base_uri' => 'https://api.africastalking.com'
        ]);
    
        try {
            $response = $client->post('/version1/messaging', [
                'form_params' => [
                    'username' => $username,
                    'to' => '+254723710025', // The number you want to test with
                    'message' => $smsContent,
                    
                ],
                'headers' => [
                    'apiKey' => $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ]
            ]);
    
            $responseBody = $response->getBody()->getContents();
            
            // Try to parse as JSON first
            $result = json_decode($responseBody, true);
            
            if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                // If JSON parsing fails, try XML
                $xml = simplexml_load_string($responseBody);
                if ($xml === false) {
                    Log::error('AfricasTalking API Response Parse Error', [
                        'response' => $responseBody
                    ]);
                    return 'Error parsing API response';
                }
                $result = $this->xmlToArray($xml);
            }
    
            Log::info('AfricasTalking API Response', ['result' => $result]);
    
            if (isset($result['SMSMessageData']['Recipients'][0]['status']) && 
                $result['SMSMessageData']['Recipients'][0]['status'] === 'Success') {
                return 'SMS sent successfully';
            } else {
                $errorMessage = $result['SMSMessageData']['Message'] ?? 'Unknown error';
                Log::error('AfricasTalking API Error', ['result' => $result]);
                return 'SMS sending failed: ' . $errorMessage;
            }
        } catch (\Exception $e) {
            Log::error('AfricasTalking API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'Error: ' . $e->getMessage();
        }
    }
    
    private function xmlToArray($xml)
    {
        $array = json_decode(json_encode((array) $xml), true);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->xmlToArray($value);
            }
        }
        return $array;
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
