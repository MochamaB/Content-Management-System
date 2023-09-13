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
    public function __construct(AssignUserToUnit $event)
    {
        $user = $event->user;
        $unitId = $event->unitId;

         // Detach the old Units from user
         $user()->unit()->detach();
        ///Attach the new ones
         $user()->unit()->attach($unitId);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }
}
