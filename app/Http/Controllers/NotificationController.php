<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewsWasPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
  public function index()
  {


// Get the user
$user = User::find(10);

// Notification data
$notificationData = new NewsWasPublished();

// Get the notification message as per your implementation
$message = $notificationData->toAfricasTalking($user);

// AfricasTalking API endpoint URL for generating auth token
$authTokenUrl = 'https://api.africastalking.com/auth-token/generate';

// AfricasTalking API endpoint URL for sending SMS
$apiUrl = 'https://api.sandbox.africastalking.com/version1/messaging';

// AfricasTalking API Key and username
$apiKey = 'cf76f240eff9bf8498a913e546ee925e2bdd357414c6da0d706582913c07fef7';
$username = 'sandbox';

// Build the request payload for generating auth token
$authTokenPayload = [
    'username' => $username,
    'apiKey' => $apiKey,
];

// Set cURL options for generating auth token
$curlOptions = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
];

// Send the request to generate auth token
$authTokenResponse = Http::withOptions($curlOptions)->post($authTokenUrl, $authTokenPayload);

// Check the response for generating auth token
if ($authTokenResponse->successful()) {
    $authToken = $authTokenResponse->json('token');

    // Build the request payload for sending SMS
    $smsPayload = [
        'to' => $user->phonenumber,  // Assuming phone_number is a field in your User model
        'message' => $message,
    ];

    // Set cURL options for sending SMS
    $curlOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];

    // Send the request using Laravel's HTTP client with auth token in headers
    $response = Http::withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => "Bearer $authToken",
    ])->withOptions($curlOptions)->post($apiUrl, $smsPayload);

    // Check the response for sending SMS
    if ($response->successful()) {
        // SMS sent successfully
        return view('admin.Communication.notification_index');
    } else {
        // Handle the error
        $errorMessage = $response->body();
        // Handle or log the error message
        echo $errorMessage;
    }
} else {
    // Handle the error for generating auth token
    $errorMessage = $authTokenResponse->body();
    // Handle or log the error message
    echo $errorMessage;
}
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
