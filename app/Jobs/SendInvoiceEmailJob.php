<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\InvoiceGeneratedNotification;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $invoice;

     public function __construct($invoice)
     {
         $this->invoice = $invoice;
     }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /$user = $this->invoice->model;
        $unitchargeId = $this->invoice->invoiceItems->pluck('unitcharge_id')->first();
        $sixMonths = now()->subMonths(6);
        $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $this->invoice->unit_id)
            ->where('unitcharge_id', $unitchargeId)
            ->get();
        $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');
        $openingBalance = $this->calculateOpeningBalance($this->invoice); // You would need to move this method to this job or a separate service

        $notification = new InvoiceGeneratedNotification($this->invoice, $user, $transactions, $groupedInvoiceItems, $openingBalance);
        $user->notify($notification);
    }
}
