<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Property;
use App\Models\User;
use App\Models\SmsCredit;
use App\Notifications\SendTextNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public function getRecipients(string $modelType, array $data)
    {
        $recipients = collect();
        $loggedUser = Auth::user();

        switch ($modelType) {
            case 'ticket':
                $property = Property::find($data['property_id']);
                $attachedUsers = $property->users()
                    ->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'tenant');
                    })
                    ->distinct()
                    ->get();
                $recipients = $attachedUsers->push($loggedUser)->unique('id');
                break;

            case 'invoice':
                $invoice = Invoice::find($data['invoice_id']);
                $tenants = $invoice->lease->tenants;
                $propertyManagers = $invoice->property->users()
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'property_manager');
                    })
                    ->get();
                $recipients = $tenants->merge($propertyManagers)->push($loggedUser)->unique('id');
                break;

            case 'lease':
                $lease = Lease::find($data['lease_id']);
                $tenants = $lease->tenants;
                $propertyManagers = $lease->property->users()
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'property_manager');
                    })
                    ->get();
                $recipients = $tenants->merge($propertyManagers)->push($loggedUser)->unique('id');
                break;

            // Add more cases as needed for different models
            
            default:
                throw new \InvalidArgumentException("Unknown model type: {$modelType}");
        }
        $recipients = $this->normalizeRecipients($recipients);

        return $recipients;
    }

    public function sendBulkSms($recipients, string $message, User $sender, string $notificationClass)
    {
        try {
            $recipients = $this->normalizeRecipients($recipients);
            $numberOfSms = count($recipients);

            //1. Check credits before processing
            if (!$this->reserveCredits($numberOfSms)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient SMS credits. Please top up.'
                ];
            }
            //2. Send SMS if theres enough credits
            $results = $this->processBulkSend($recipients, $message, $sender,$notificationClass);

            //3. Finalize the transaction

            $this->finalizeCreditTransaction($results['success_count'], $results['fail_count']);

            return [
                'success' => true,
                'message' => "Successfully sent {$results['success_count']} messages. Failed to send {$results['fail_count']} messages.",
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
        $loggedInUser = Auth::user();  // Get the logged-in user
        $properties = $loggedInUser->properties->pluck('id');
        // Check if user has specific credits
        $propertyCredits = SmsCredit::where('credit_type', SmsCredit::TYPE_PROPERTY)
                           //   ->where('property_id', $properties)
                               ->exists();
    
        if ($propertyCredits) {
            return [
                'type' => SmsCredit::TYPE_PROPERTY,
               'condition' => ['property_id' => $properties->all()]
            ];
        }

        // Check if user has specific credits
        $userCredits = SmsCredit::where('credit_type', SmsCredit::TYPE_USER)
                               ->where('user_id', auth()->id())
                               ->exists();
        
        if ($userCredits) {
            return [
                'type' => SmsCredit::TYPE_USER,
                'condition' => ['user_id' => auth()->id()]
            ];
        }

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
    protected function processBulkSend($recipients, string $message, User $sender, string $notificationType): array
    {
        $successCount = 0;
        $failCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $recipient) {
            try {
                $user = $recipient instanceof User ? $recipient : User::where('phonenumber', $recipient)->first();
                if ($user) {
                    $notification = new $notificationType($user, $message, $sender);
                    $user->notify($notification);
                    $successCount++;
                } else {
                    $failCount++;
                    $failedRecipients[] = $recipient instanceof User ? $recipient->phonenumber : $recipient;
                }
            } catch (Exception $e) {
                Log::error("SMS Send Error for recipient {$recipient}: " . $e->getMessage());
                $failCount++;
                $failedRecipients[] = $recipient instanceof User ? $recipient->phonenumber : $recipient;
            }
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
    protected function finalizeCreditTransaction(int $successCount, int $failCount): void
    {
        if (!$this->creditEntry) return;

        DB::beginTransaction();
        try {
            $instanceCredits = SmsCredit::where('credit_type', SmsCredit::TYPE_INSTANCE)->first();
            
            if ($successCount > 0) {
                // Update instance credits
                $instanceCredits->blocked_credits -= $successCount;
                $instanceCredits->used_credits += $successCount;
                
                // Update specific credits
                $this->creditEntry->blocked_credits -= $successCount;
                $this->creditEntry->used_credits += $successCount;
            }

            if ($failCount > 0) {
                // Return failed credits to available
                $instanceCredits->blocked_credits -= $failCount;
                $instanceCredits->available_credits += $failCount;
                
                $this->creditEntry->blocked_credits -= $failCount;
                $this->creditEntry->available_credits += $failCount;
            }

            $instanceCredits->save();
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
        if (!$this->creditEntry || !$this->pendingCredits) return;

        DB::beginTransaction();
        try {
            $instanceCredits = SmsCredit::where('credit_type', SmsCredit::TYPE_INSTANCE)->first();
            
            // Return all pending credits to available
            $instanceCredits->blocked_credits -= $this->pendingCredits;
            $instanceCredits->available_credits += $this->pendingCredits;
            $instanceCredits->save();

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
}