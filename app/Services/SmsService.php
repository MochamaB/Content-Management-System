<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Notification;
use App\Models\Property;
use App\Models\User;
use App\Models\SmsCredit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;

class SmsService
{
    protected $creditEntry = null;
    protected $pendingCredits = 0;
    
    /**
     * Send SMS to multiple recipients
     *
     * @param Collection|array $recipients Users or phone numbers to send to
     * @param string $message Message content
     * @param User $sender User sending the message
     * @return array
     */

     
    public function queueSmsNotification($recipient,string $notificationClass, array $notificationParams)
    {
        try {
         //   $recipients = $this->normalizeRecipients($recipients);
            $numberOfSms = 1;

            //1. Check credits before processing
            if (!$this->reserveCredits($numberOfSms)) {
               
                    $this->logNotification($notificationClass, $notificationParams, 'failed');
                
                return [
                    'success' => false,
                    'message' => 'Insufficient SMS credits. Please top up.'
                ];
            }
            //2. Send SMS if theres enough credits
             $results = $this->sendText($recipient, $notificationClass, $notificationParams);

            //3. Finalize the transaction

         //   $this->finalizeCreditTransaction($results['success_count'], $results['fail_count']);

            return [
                'success' => true,
                'message' => "Successfully Queued {$results['success_count']} messages. Failed to Queue {$results['fail_count']} messages.",
                'failed_recipients' => $results['failed_recipients']
            ];

        } catch (Exception $e) {
            Log::error('SMS Sending Error: ' . $e->getMessage());
            $this->releaseAllCredits();
            
            return [
                'success' => false,
                'message' => 'Failed to process SMS sending: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reserve SMS credits before sending
     *
     * @param int $numberOfSms
     * @return bool
     */

     protected function determineCreditType(): array
    {
       
        return [
            'type' => SmsCredit::TYPE_INSTANCE,
            'condition' => []
        ];
    }

    public function reserveCredits(int $numberOfSms): bool
    {
        DB::beginTransaction();
        try {
            //1. Check credit Type
            $creditType = $this->determineCreditType();
          
            //2. Get credit entry with lock
            $creditEntry = SmsCredit::where('credit_type', $creditType['type']);
           
            foreach ($creditType['condition'] as $key => $value) {
                $creditEntry->where($key, $value);
            }
            
            $creditEntry = $creditEntry->lockForUpdate()->first();
            
            if (!$creditEntry || $creditEntry->available_credits < $numberOfSms) {
                DB::rollBack();
                return false;
            }

            $creditEntry->available_credits -= $numberOfSms;
            $creditEntry->blocked_credits += $numberOfSms;
            $creditEntry->save();

            $this->creditEntry = $creditEntry;
            $this->pendingCredits = $numberOfSms;

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Credit reservation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process bulk SMS sending
     *
     * @param Collection $recipients
     * @param string $message
     * @param User $sender
     * @return array
     */
    protected function sendText($recipient, string $notificationClass, array $notificationParams): array
    {
        $successCount = 0;
        $failCount = 0;
        $failedRecipients = [];
        
       
            try {
                $user = $recipient instanceof User ? $recipient : User::where('phonenumber', $recipient)->first();
                if ($user) {
                    
                // Wrap notification in a try-catch to handle actual send result
                    try {
                        $notification = $this->logNotification($notificationClass, $notificationParams, 'pending');
                        $user->notify($notification); // Send notification
                        $successCount++; // Only count if no exception is thrown
                    } catch (\Exception $e) {
                        Log::error('Failed to send notification for user ' . $user->phonenumber . ': ' . $e->getMessage());
                        $failCount++;
                        $failedRecipients[] = $user->phonenumber;
                    }
                } else {
                    $failCount++;
                    $failedRecipients[] = $recipient instanceof User ? $recipient->phonenumber : $recipient;
                }
            } catch (Exception $e) {
                Log::error("SMS Send Error for recipient {$recipient->phonenumber}: " . $e->getMessage());
                $failCount++;
                $failedRecipients[] = $recipient instanceof User ? $recipient->phonenumber : $recipient;
            }
        

        return [
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'failed_recipients' => $failedRecipients
        ];
    }

    /**
     * Finalize credit transaction after sending
     *
     * @param int $successCount
     * @param int $failCount
     * @return void
     */
    public function finalizeCreditTransaction(int $successCount, int $failCount): void
    {
        //1. Check credit Type
        $creditType = $this->determineCreditType();
          
        //2. Get credit entry with lock
        $creditEntry = SmsCredit::where('credit_type', $creditType['type']);
       
        foreach ($creditType['condition'] as $key => $value) {
            $creditEntry->where($key, $value);
        }
        
        $creditEntry = $creditEntry->lockForUpdate()->first();
        $this->creditEntry = $creditEntry;
       
        if (!$this->creditEntry || !$this->pendingCredits) return;

        DB::beginTransaction();
        try {
         
            if ($successCount > 0) {
                
                // Update specific credits
                $this->creditEntry->blocked_credits -= $successCount;
                $this->creditEntry->used_credits += $successCount;
            }

            if ($failCount > 0) {
               
                $this->creditEntry->blocked_credits -= $failCount;
                $this->creditEntry->available_credits += $failCount;
            }

            $this->creditEntry->save();
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Credit finalization failed: ' . $e->getMessage());
        }
    }

    /**
     * Release all pending credits back to available
     *
     * @return void
     */
    public function releaseAllCredits(): void
    {
        //1. Check credit Type
        $creditType = $this->determineCreditType();
          
        //2. Get credit entry with lock
        $creditEntry = SmsCredit::where('credit_type', $creditType['type']);
       
        foreach ($creditType['condition'] as $key => $value) {
            $creditEntry->where($key, $value);
        }
        $creditEntry = $creditEntry->lockForUpdate()->first();

        $this->creditEntry = $creditEntry;
        $this->pendingCredits = 1;

        Log::info('Attempting to release all credits.', [
            'credit_entry_exists' => (bool)$this->creditEntry,
            'pending_credits' => $this->pendingCredits
        ]);
        if (!$this->creditEntry || !$this->pendingCredits) {
            Log::warning('Attempted to release credits with no pending transaction', [
                'credit_entry_exists' => (bool)$this->creditEntry,
                'pending_credits' => $this->pendingCredits,
                'credits' => $this->creditEntry
            ]);
            return;
        }
        
        DB::beginTransaction();
        try {
            
             // 3. Update the credit amounts (release the credits)
             $this->creditEntry->blocked_credits -= $this->pendingCredits;
             $this->creditEntry->available_credits += $this->pendingCredits;
             $this->creditEntry->save();


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Credit release failed: ' . $e->getMessage());
        }
    }

    /**
     * Normalize recipients to collection
     *
     * @param mixed $recipients
     * @return Collection
     */
    protected function normalizeRecipients($recipients)
    {
        if (is_array($recipients)) {
            return collect($recipients);
        }
        
        if ($recipients instanceof Collection) {
            return $recipients;
        }

        return collect([$recipients]);
    }

    protected function logNotification($notificationClass, array $notificationParams, $status)
        {
             // Reflection to get constructor parameters
        $reflector = new \ReflectionClass($notificationClass);
        $constructorParams = $reflector->getConstructor()->getParameters();
        // Instantiate the notification with required arguments
        $args = [];
        foreach ($constructorParams as $param) {
            $paramName = $param->getName();
            if (array_key_exists($paramName, $notificationParams)) {
                $args[] = $notificationParams[$paramName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new \InvalidArgumentException("Missing required parameter: $paramName");
                Log::error('Missing required parameter: ' .$paramName);
            }
        }
         // Instantiate the notification with matched arguments
         $notification = $reflector->newInstanceArgs($args);
        // Generate SMS content without sending
         $smsContent = method_exists($notification, 'generateSmsContent') ? $notification->generateSmsContent() : null;
          // Prepare the data array for JSON encoding
            $data = [
                'user_id' => $notificationParams['user']->id ?? null,
                'to' => $notificationParams['user']->phonenumber ?? $notificationParams['recipient'] ?? null,
                'from' => 'System Generated',
                'sms_content' => $notification->generateSmsContent() ?? null, // Generate SMS content if available
                'status' => $status,
                'channels' => $notification->via($notificationParams['user'] ?? null), // Get notification channels
            ];
             // Convert the data array to JSON
            $jsonData = json_encode($data);
        // Save the notification record to the database if pending but return if sent
        if ($status === 'pending') {

            return $notification;

        } else {

            Notification::create([
                'id' => Str::uuid(),
                'type' => $notificationClass,
                'notifiable_type' => get_class($notificationParams['user'] ?? null),
                'notifiable_id' => $notificationParams['user']->id ?? null,
                'data' => $jsonData,
                'sms_status' => $status,
                'created_at' => now()
            ]);
        }

            
        }
    }