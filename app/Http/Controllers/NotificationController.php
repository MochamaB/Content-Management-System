<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewsWasPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $invoice = Invoice::find(1);
     /// Data for the Account Statement
     $unitchargeId = $invoice->invoiceItems->pluck('unitcharge_id')->first();
     //    dd($unitchargeIds);
     $sixMonths = now()->subMonths(6);
     $transactions = Transaction::where('created_at', '>=', $sixMonths)
         ->where('unit_id', $invoice->unit_id)
         ->where('unitcharge_id', $unitchargeId)
         ->get();
     $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');

     ////Opening Balance
     $openingBalance = $this->calculateOpeningBalance($invoice);

     //// Data for the Payment Methods
     $PaymentMethod = PaymentMethod::where('property_id',$invoice->property_id)->get();

    return view('email.template2', compact('user','invoice','transactions','groupedInvoiceItems','openingBalance'));

  }

  public function calculateOpeningBalance(Invoice $invoice)
  {
      // Get the date 6 months ago from today
      $sixMonthsAgo = now()->subMonths(6);

      // Calculate the sum of invoice amounts
      $invoiceAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
          ->where('unit_id', $invoice->unit_id)
          ->where('charge_name', $invoice->type)
          ->where('transactionable_type', 'App\Models\Invoice')
          ->sum('amount');

      // Calculate the sum of payment amounts
      $paymentAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
          ->where('unit_id', $invoice->unit_id)
          ->where('charge_name', $invoice->type)
          ->where('transactionable_type', 'App\Models\Payment')
          ->sum('amount');

      // Calculate the opening balance
      $openingBalance = $invoiceAmount - $paymentAmount;

      return $openingBalance;
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
