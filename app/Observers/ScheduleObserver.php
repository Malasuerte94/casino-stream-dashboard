<?php

namespace App\Observers;

use App\Http\Controllers\DiscordController;
use App\Models\Schedule;

class ScheduleObserver
{
    /**
     * Handle the Schedule "created" event.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    public function created(Schedule $schedule): void
    {
        $discord = new DiscordController();
        $discord->sendScheduleMessage($schedule->id);
    }

    /**
     * Handle the Schedule "updated" event.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    public function updated(Schedule $schedule)
    {
        //
    }

    /**
     * Handle the Schedule "deleted" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function deleted(Schedule $schedule)
    {
        //
    }

    /**
     * Handle the Schedule "restored" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function restored(Schedule $schedule)
    {
        //
    }

    /**
     * Handle the Schedule "force deleted" event.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return void
     */
    public function forceDeleted(Schedule $schedule)
    {
        //
    }
}
