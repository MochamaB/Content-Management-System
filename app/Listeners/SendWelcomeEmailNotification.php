<?php

namespace App\Listeners;

use App\Events\UserCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreate  $event
     * @return void
     */
    public function handle(UserCreate $event)
    {
        $userinfo = $event->user;

        $data = ([
            "name" => $userinfo->firstname,
            "email" => "",
            "username" => "",
            "phone" => "",
            ]);
           Mail::to('adminone@gmail.com')->send(new WelcomeMail($data));
           return redirect()->back()->with('status','Email Sent');
    }
}
