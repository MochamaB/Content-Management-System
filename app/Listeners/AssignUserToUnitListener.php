<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AssignUserToUnit;

class AssignUserToUnitListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AssignUserToUnit $event)
    {
        $user = $event->user;
        $unitId = $event->unitId;
        $propertyId = $event->propertyId;

        // Detach the old Units from user
        $user->units()->detach();

        // Attach the new ones
        $user->units()->attach($unitId, ['property_id' => $propertyId]);
    }
}
