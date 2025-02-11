<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Events\BonusBuyOrHuntUpdated;

class BonusBuyOrBattleObserver
{
    public function created(Model $model): void
    {
        event(new BonusBuyOrHuntUpdated($model));
    }

    public function updated(Model $model): void
    {
        event(new BonusBuyOrHuntUpdated($model));
    }

    public function deleted(Model $model): void
    {
        event(new BonusBuyOrHuntUpdated($model));
    }
}
