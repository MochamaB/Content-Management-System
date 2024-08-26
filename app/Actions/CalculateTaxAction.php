<?php

// app/Actions/UpdateDueDateAction.php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Tax;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class CalculateTaxAction
{
    use AsAction;

    public function calculateTax(Model $model)
    {
        $taxes = Tax::where('model_type', get_class($model))
            ->where('property_type_id', $model->property->property_type)
            ->where('status', 'active')
            ->get();

        $totalTax = 0;
        foreach ($taxes as $tax) {
            $totalTax += $model->totalamount * ($tax->rate / 100);
        }
       
        // Update the due_date column in the invoices table
        $model->update(['taxamount' => $totalTax]);
    }

   

}