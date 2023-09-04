<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index()
    {

    }

    public function show()
    {
        $user = Auth::user();
        $notification = $user->notifications;
       
        return View('layouts.admin.notification', compact('notification'));
    }
}
