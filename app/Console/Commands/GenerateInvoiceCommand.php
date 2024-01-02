<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;
use App\Models\Task;

    
class GenerateInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:invoice';
    private $invoiceService;




    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */

     public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
    }
    public function handle()
    {
        $task = Task::where('type', 'invoice')->first();

        if ($task->status == 1) {
            $this->invoiceService->chargesForInvoiceGeneration();
            $this->info('Invoice Generated Successfully.');

        } else {
            $this->info('Task is Inactive.');
        }
    }
}
