<?php

namespace App\Observers;

use App\Events\SettingsUpdated;
use Illuminate\Database\Eloquent\Model;

class SettingsObserver
{
    public function created(Model $model): void
    {
        event(new SettingsUpdated($model));
    }

    public function updated(Model $model): void
    {
        event(new SettingsUpdated($model));
    }

    public function deleted(Model $model): void
    {
        event(new SettingsUpdated($model));
    }
}
