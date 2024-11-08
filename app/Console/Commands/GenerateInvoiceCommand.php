<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoiceJob;
use Illuminate\Console\Command;
use App\Services\InvoiceService;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

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
        $task = Task::where('command', 'generate:invoice')->first();
        if ($task->status != 1) {
            $this->info('Task is Inactive.');
            return;
        }
        try {
            // Dispatch a single job to handle all invoice generation
            GenerateInvoiceJob::dispatch($this->invoiceService, $task->id)
                ->onQueue('Generateinvoices');

            // Log success of job dispatch
            Log::info('Invoice generation job dispatched', [
                'command' => $this->signature,
                'timestamp' => now()
            ]);

            $this->info('Invoice generation job queued successfully.');

        } catch (\Exception $e) {
            // Log failure
            Log::error('Failed to dispatch invoice generation job', [
                'command' => $this->signature,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Failed to queue invoice generation job: ' . $e->getMessage());
            
            throw $e;
        }

        if ($task->status == 1) {
            $this->invoiceService->chargesForInvoiceGeneration();
            $this->info('Invoice Generated Successfully.');

        } else {
            $this->info('Task is Inactive.');
        }
    }
}
