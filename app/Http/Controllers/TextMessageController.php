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
use App\Models\Ticket;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use ReflectionClass;

class TextMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
       
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Dashboard') {
                $tabContents[] = View('admin.Communication.text_inbox')->render();
            }elseif ($title === 'Inbox') {
                $tabContents[] = View('admin.Communication.text_summary',compact('textContent'))->render();
            }elseif ($title === 'Compose Text') {
                $tabContents[] = View('admin.Communication.text_inbox')->render();
            }elseif ($title === 'Top up') {
                $tabContents[] = View('admin.Communication.text_inbox')->render();
            }elseif ($title === 'Transactions') {
                $tabContents[] = View('admin.Communication.text_inbox')->render();
            }elseif ($title === 'Tarriffs') {
                $tabContents[] = View('admin.Communication.text_inbox')->render();
            }
        }

        return View('admin.Communication.text', compact('tabTitles', 'tabContents'));
        //
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
            $notificationData  = json_decode($notification->data, true);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
