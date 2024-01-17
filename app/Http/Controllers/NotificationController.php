<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    if (Gate::allows('view-all', $user)) {
      $notifications = Notification::all();
      $unreadNotifications = $notifications->where('read_at', null);
      $readNotifications = $notifications->where('read_at', '!=', null);
      dd($unreadNotifications);
    } else {
      $unreadNotifications = $user->unreadNotifications;
      $readNotifications = $user->readNotifications;
    }
    //  $notifications = Notification::all();
    //  $notifications = auth()->user()->notifications;
    return view('admin.Communication.email', [
      'unreadNotifications' => $unreadNotifications,
      'readNotifications' => $readNotifications,
    ]);
  }
  public function email()
  {
    $user = Auth::user();
    if (Gate::allows('view-all', $user)) {
      $notifications = Notification::all();
      $unreadNotifications = $notifications->where('read_at', null);
      $readNotifications = $notifications->where('read_at', '!=', null);
      // dd($unreadNotifications);
    } else {
      $unreadNotifications = $user->unreadNotifications;
      $readNotifications = $user->readNotifications;
    }

    return view('admin.Communication.notification ', [
      'unreadNotifications' => $unreadNotifications,
      'readNotifications' => $readNotifications,
    ]);
  }

  public function show(Notification $notification)
  {
    return view('admin.Communication.show', compact('notification'));
  }
}
