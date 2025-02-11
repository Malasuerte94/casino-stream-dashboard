<?php

namespace App\Events;

use App\Models\BattleViewer;
use App\Traits\BroadcastsToUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BattleViewerUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels, BroadcastsToUser;

    public array $data;

    public function __construct(Model $model)
    {
        $this->data = $model->toArray();
    }

    public function broadcastOn(): Channel
    {
        $userID = $this->data['user_id'];
        return new Channel('App.Models.User.' . $userID);
    }

}
