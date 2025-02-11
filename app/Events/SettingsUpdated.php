<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Traits\BroadcastsToUser;

class SettingsUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels, BroadcastsToUser;

    public array $data;

    public function __construct(Model $model)
    {
        $this->data = $model->toArray();
    }
}
