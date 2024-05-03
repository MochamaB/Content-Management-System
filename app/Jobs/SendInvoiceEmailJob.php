<?php

namespace App\Jobs;

use App\Models\Transaction;
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
     protected $user;
     protected $transactions;
     protected $groupedInvoiceItems;
     protected $openingBalance;

     public function __construct($invoice, $user, $transactions, $groupedInvoiceItems, $openingBalance)
    {
        $this->invoice = $invoice;
        $this->user = $user;
        $this->transactions = $transactions;
        $this->groupedInvoiceItems = $groupedInvoiceItems;
        $this->openingBalance = $openingBalance;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new InvoiceGeneratedNotification($this->invoice, $this->user, $this->transactions, $this->groupedInvoiceItems, $this->openingBalance));
    }
}
