<?php

// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNextDateAction
{
    use AsAction;

    public function handle(string $chargeCycle, string $startDate): Carbon
    {
        $startDate = Carbon::parse($startDate);
        $nextDate = null;

        if ($chargeCycle === 'Monthly') {
            $nextDate = $startDate->addMonth();
        } elseif ($chargeCycle === 'Twomonths') {
            $nextDate = $startDate->addMonths(2);
        } elseif ($chargeCycle === 'Quarterly') {
            $nextDate = $startDate->addMonths(3);
        } elseif ($chargeCycle === 'Halfyear') {
            $nextDate = $startDate->addMonths(6);
        } elseif ($chargeCycle === 'Year') {
            $nextDate = $startDate->addYear();
        }

        return $nextDate;
    }
}
