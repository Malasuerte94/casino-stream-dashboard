<?php

namespace App\Traits;

use Illuminate\Broadcasting\Channel;
trait BroadcastsToUser
{
    /**
     * Automatically return a private channel for the event.
     * It expects the event to have a "user_id" property.
     */
    public function broadcastOn(): Channel
    {
        $userID = auth()->user()->id;
        return new Channel('App.Models.User.' . $userID);
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
