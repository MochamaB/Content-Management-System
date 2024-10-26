<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\SmsCredit;
use App\Models\SmsTopup;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\SendTextNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use ReflectionClass;
use App\Services\CardService;
use App\Services\TableViewDataService;
use App\Services\SmsService;

class TextMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     private $cardService;
     private $tableViewDataService;
     protected $smsService;

     public function __construct(TableViewDataService $tableViewDataService, CardService $cardService,SmsService $smsService)
    {
        $this->cardService = $cardService;
        $this->tableViewDataService = $tableViewDataService;
        $this->smsService = $smsService;
    }
    public function index()
    {


        $tabTitles = collect([
            'Dashboard',
            'Inbox',
            'Compose Text',
            'Top up',
            'Transactions',
            'Tarriffs',
        ]);

        $textContent = $this->textInbox();
        $users = User::with('units','roles')->visibleToUser()->get();
        $roles  = User::getDistinctRolesFromUsers($users);
        $transactions = $this->textTransactions();
        $tableData = $this->tableViewDataService->getTransactionData($transactions, false);
        $tariffs = $this->textTariff();
        $tariffData = $this->tableViewDataService->getTariffData($tariffs, false);
      //  dd($textContent->count());
       
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Dashboard') {
                $tabContents[] = View('admin.Dashboard.smscredit', compact('textContent','tariffs'))->render();
            }elseif ($title === 'Inbox') {
                $tabContents[] = View('admin.Communication.text_summary',compact('textContent'))->render();
            }elseif ($title === 'Compose Text') {
                $tabContents[] = View('admin.Communication.send_text',compact('users','roles'))->render();
            }elseif ($title === 'Top up') {
                $tabContents[] = View('admin.Communication.text_topup')->render();
            }elseif ($title === 'Transactions') {
                $tabContents[] = View('admin.CRUD.table', ['data' => $tableData,'controller' => ['textMessage']])->render();
            }elseif ($title === 'Tarriffs') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $tariffData,'controller' => ['smsCredit']])->render();
            }
        }

        return View('admin.Communication.text', compact('tabTitles', 'tabContents'));
        //
    }

    public function checkCredits(Request $request)
    {
        try {
            $modelType = $request->input('model_type');
            $data = $request->all();

            // Get recipients based on model type
            $recipients = $this->smsService->getRecipients($modelType, $data);
            $numberOfSms = count($recipients);

            // Check credits
            $hasCredits = $this->smsService->reserveCredits($numberOfSms);

            if ($hasCredits) {
                $this->smsService->releaseAllCredits();
                return response()->json([
                    'hasCredits' => true,
                    'message' => 'Credits available'
                ]);
            }

            return response()->json([
                'hasCredits' => false,
                'message' => 'Insufficient SMS credits available. Would you like to proceed with email notifications only?'
            ]);

        } catch (\Exception $e) {
            Log::error('Credit check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    public function textInbox()
    {
        $user = Auth::user();
        $perPage = 20;
        $page = Paginator::resolveCurrentPage() ?: 1;
        // Check if the user has permission to view all notifications
        if (Gate::allows('view-all', $user)) {
           // $notifications = Notification::all();
            $mailNotifications = Notification::whereJsonContains('data->channels', ['NotificationChannels\AfricasTalking\AfricasTalkingChannel'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

            $unreadNotifications = $mailNotifications->where('read_at', null);
            $readNotifications = $mailNotifications->where('read_at', '!=', null);
       
        } else {
             // For users with restricted access, filter their notifications
             $allNotifications = $user->notifications()
            ->whereJsonContains('data->channels', ['NotificationChannels\AfricasTalking\AfricasTalkingChannel'])
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
            $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
            $notificationType = $notification->type;
                // Extract the SMS content from the notification data
                $smsContent = $notificationData['sms_content'] ?? 'No SMS content available'; // Default if missing

                // Attach SMS content to the notification object
                $notification->sms_content = $smsContent;

                return $notification;
    
        });
         // Return inbox notifications
         return $inboxNotifications;
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

    public function textTransactions()
    {
        $perPage = 20;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $transactions = SmsTopup::orderBy('created_at', 'desc')
        ->paginate($perPage);

        return $transactions;
    }

    public function textTariff()
    {
        $perPage = 20;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $tariffs = SmsCredit::orderBy('created_at', 'desc')
        ->paginate($perPage);

        return $tariffs;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loggedUser = Auth::user();
        $request->validate([
            'send_to' => 'required',
            'message' => 'required|string',
            'users' => 'nullable|array', // In case contacts are selected
            'group' => 'nullable|string', // In case a group is selected
        ]);
    
        $message = $request->input('message');
        $recipients = [];
    
        // If the user chose "contact", get the selected users
        if ($request->input('send_to') === 'contact') {
            $recipients  = $request->input('users', []); // Array of phone numbers
        }
    
        // If the user chose "group", get all users in that group/role
        elseif ($request->input('send_to') === 'group') {
            $group = $request->input('group');
    
            // Fetch all users associated with the selected role/group
            $recipients = User::whereHas('roles', function ($query) use ($group) {
                $query->where('name', $group);
            })->visibleToUser()->get();
        }

        // Check if it's a text or email notification based on request
        $notificationClass = SendTextNotification::class;
        $result = $this->smsService->sendBulkSms($recipients, $message, $loggedUser,$notificationClass);

        if (!$result['success']) {
            return redirect()->back()->with('statuserror', $result['message']);
        }
    
        return redirect()->back()->with('status',  $result['message']);
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
