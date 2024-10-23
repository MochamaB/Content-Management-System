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
use App\Notifications\SendEmailNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use ReflectionClass;


class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::with('units','roles')->visibleToUser()->get();
        $roles  = User::getDistinctRolesFromUsers($users);

         ///SESSION /////
         if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Communication.create_email', compact('users','roles'));
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
            'subject' => 'required|string',
            'message' => 'required|string',
            'users' => 'nullable|array', // In case contacts are selected
            'group' => 'nullable|string', // In case a group is selected
        ]);
    
        $subject = $request->input('subject');
        $message = $request->input('message');
    
        // If the user chose "contact", get the selected users
        if ($request->input('send_to') === 'contact') {
            $selectedUsers = $request->input('users', []); // Array of email
    
            // Trigger the notification for each selected user
            foreach ($selectedUsers as $email) {
                // Assuming you have a way to retrieve user by phone number
                $user = User::where('email', $email)->first();
    
                if ($user) {
                    $user->notify(new SendEmailNotification($user,$subject,$message,$loggedUser));
                }
            }
        }
    
        // If the user chose "group", get all users in that group/role
        elseif ($request->input('send_to') === 'group') {
            $group = $request->input('group');
    
            // Fetch all users associated with the selected role/group
            $usersInGroup = User::whereHas('roles', function ($query) use ($group) {
                $query->where('name', $group);
            })->visibleToUser()->get();
    
            // Trigger the notification for each user in the group
            foreach ($usersInGroup as $user) {
                $user->notify(new SendEmailNotification($user,$subject,$message,$loggedUser));
            }
        }

        $redirectUrl = session()->pull('previousUrl', 'email');

        return redirect($redirectUrl)->with('status','Email  Sent Successfully');
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
