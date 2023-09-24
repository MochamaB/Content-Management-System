<?php

namespace App\Events;

use App\Models\Property;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Unit;

class AssignUserToUnit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $unitId;
    public $propertyId;

    /**
     * Create a new event instance.
     *
     * @param \App\User $user
     * @param int $unitId
     * @return void
     */
    public function __construct(User $user,Unit $unitId,$propertyId)
    {
        $this->user = $user;
        $this->unitId = $unitId;
        $this->propertyId = $propertyId;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
