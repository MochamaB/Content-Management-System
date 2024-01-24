<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewsWasPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use AfricasTalking\SDK\AfricasTalking;

class NotificationController extends Controller
{
  public function index()
  {
    return view('admin.Communication.notification_index');
  }
  public function email()
    {
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
  }



  public function show($uuid)
{
    $notificationData = Notification::find($uuid);
  // dd($notification);
    return view('admin.Communication.notification ', [
      'notificationData' => $notificationData,
    ]);
}
}
