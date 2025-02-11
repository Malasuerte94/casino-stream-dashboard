<?php

namespace App\Observers;

use App\Events\BattleViewerUpdated;
use App\Models\BattleViewer;
use App\Events\BonusBuyOrHuntUpdated;

class BattleViewerObserver
{
    public function created(BattleViewer $battleViewer): void
    {
        event(new BattleViewerUpdated($battleViewer));
    }

    public function updated(BattleViewer $battleViewer): void
    {
        event(new BattleViewerUpdated($battleViewer));
    }

    public function deleted(BattleViewer $battleViewer): void
    {
        event(new BattleViewerUpdated($battleViewer));
    }
}
