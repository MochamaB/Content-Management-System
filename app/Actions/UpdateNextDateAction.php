<?php

// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNextDateAction
{
    use AsAction;

    public function handle(string $chargeCycle, string $startDate): Carbon
    {
        $startDate = Carbon::parse($startDate);
        $monthsToAdd = match ($chargeCycle) {
            'Monthly' => 1,
            'Twomonths' => 2,
            'Quarterly' => 3,
            'Halfyear' => 6,
            'Year' => 12,
            'once'=>0,
            default => throw new \InvalidArgumentException("Invalid charge cycle: $chargeCycle"),
        };

        $nextDate = $startDate->copy()->addMonths($monthsToAdd)->startOfMonth();
        
        return $nextDate;
    }

    public function invoicenextdate(Unitcharge $unitcharge)
    {
        $chargeCycle = $unitcharge->charge_cycle;
        $startDate = Carbon::parse($unitcharge->nextdate);
      
        $monthsToAdd = match ($chargeCycle) {
            'Monthly' => 1,
            'Twomonths' => 2,
            'Quarterly' => 3,
            'Halfyear' => 6,
            'Year' => 12,
            'once'=>0,
            default => throw new \InvalidArgumentException("Invalid charge cycle: $chargeCycle"),
        };
        $nextDate = $startDate->copy()->addMonths($monthsToAdd)->startOfMonth();
        // Update the nextdate attribute in the Unitcharge model
        $unitcharge->update(['nextdate' => $nextDate]);
        return $nextDate;
    }
}
