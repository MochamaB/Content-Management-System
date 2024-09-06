<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class AttachDetachUserFromUnitAction
{
    use AsAction;

    public function assign(User $user, $unitIds, Request $request)
    {
        // Detach the old units from the user
        $user->units()->detach();

        if (is_array($unitIds)) {
            foreach ($unitIds as $unitId => $selected) {
                if ($selected) {
                    // Retrieve the corresponding property_id from the hidden field
                    $propertyId = $request->input("property_id.{$unitId}");

                    // Attach the unit to the user with the associated property_id
                    $user->units()->attach($unitId, ['property_id' => $propertyId]);
                }
            }
        }
    }

    public function assignFromView(User $user, $unitIds, Request $request)
    {
        dd($request->all());
        // Detach the old units from the user
        $user->units()->detach();

        if (is_array($unitIds)) {
            foreach ($unitIds as $unitId => $selected) {
                if ($selected) {
                    // Retrieve the corresponding property_id from the hidden field
                    $propertyId = $request->input("property_id.{$unitId}");

                    // Attach the unit to the user with the associated property_id
                    $user->units()->attach($unitId, ['property_id' => $propertyId]);
                }
            }
        }
    }

    public function detach(User $user, Unit $unit, $propertyId)
    {
        // Detach the old units from the user
        $user->units()->detach();
    }
}
