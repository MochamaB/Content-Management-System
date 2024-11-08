<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600; 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $invoiceService;
    protected $taskId;

    public function __construct(InvoiceService $invoiceService, $taskId)
    {
        $this->invoiceService = $invoiceService;
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Starting batch invoice generation');
            
            // Call the existing service method to generate all invoices
              $this->invoiceService->chargesForInvoiceGeneration();
            
            Log::info('Batch invoice generation completed successfully');

        } catch (\Exception $e) {
            Log::error('Failed to generate invoices batch', [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }
    public function failed(\Exception $exception)
    {
        // Optional: Log or notify about job failure
        Log::error("GenerateInvoiceJob failed: " . $exception->getMessage());
    }
}
