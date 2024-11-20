<?php

// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNextDateAction
{
    use AsAction;

    public function handle(string $chargeCycle, string $startDate, string $chargeType): array
    {
        $startDate = Carbon::parse($startDate);
        // Logic for charge_type
    if ($chargeType === 'fixed') {
        // Check if the day is not the 1st of the month
        if ($startDate->day !== 1) {
            // Set startDate to the 1st of the next month
            $startDate->addMonthNoOverflow()->startOfMonth();
        }
    }
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
        
        return [
            'updatedAt' => $startDate,
            'nextDate' => $nextDate
        ];
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

    public function newChargeNextdate(Unitcharge $unitcharge)
    {
        $chargeCycle = $unitcharge->charge_cycle;
        $chargeType = $unitcharge->charge_type;
        $startDate = Carbon::parse($unitcharge->startdate);
       
        // Logic for charge_type
        if ($chargeType === 'fixed') {
            // Check if the day is not the 1st of the month
            if ($startDate->day !== 1) {
                // Set startDate to the 1st of the next month
                $startDate->addMonthNoOverflow()->startOfMonth();
            }
        }
      
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
        $unitcharge->update(['nextdate' => $nextDate,
                            'updated_at' => $startDate]);
        return $nextDate;
    }
}
