<?php

// app/Actions/UpdateDueDateAction.php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Setting;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDueDateAction
{
    use AsAction;

    public function handle(Invoice $invoice)
    {
        // Get the due date setting from the settings table
        $dueDateSetting = Setting::where('settingable_type', 'lease')
                                ->where('setting_name', 'like', '%duedate%')->first();
        // Default due date offset (in days) if the setting is not found
        $defaultDueDateOffset = 15;
        $startDate = Carbon::parse($invoice->created_at);

        if ($dueDateSetting && is_numeric($dueDateSetting->setting_value)) {
            // Calculate the due date by adding the offset to the invoice generation date
            $dueDate = $startDate->addDays($dueDateSetting->setting_value);
        } else {
            // Use the default due date offset
            $dueDate = $startDate->addDays($defaultDueDateOffset);
        }
       
        // Update the due_date column in the invoices table
        $invoice->update(['duedate' => $dueDate]);
    }

    public function duedate($leaseId)
    {
        
        Setting::create([
            'settingable_type' => 'Lease',
            'settingable_id' =>$leaseId,
            'setting_name' => 'invoiceduedate',
            'setting_value' => 15,
            'setting_description' => 'Invoices will be due after no of days of the given value',
        ]);
    }

}