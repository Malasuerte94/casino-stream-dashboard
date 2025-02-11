<?php

namespace App\Observers;

use App\Events\BonusBattleUpdated;
use Illuminate\Database\Eloquent\Model;

class BonusBattleObserver
{
    public function created(Model $model): void
    {
        event(new BonusBattleUpdated($model));
    }

    public function updated(Model $model): void
    {
        event(new BonusBattleUpdated($model));
    }

    public function deleted(Model $model): void
    {
        event(new BonusBattleUpdated($model));
    }
}
