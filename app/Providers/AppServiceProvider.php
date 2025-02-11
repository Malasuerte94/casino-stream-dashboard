<?php

namespace App\Providers;

use App\Models\BattleViewer;
use App\Models\BonusBattle;
use App\Models\BonusBuy;
use App\Models\BonusBuyGame;
use App\Models\BonusConcurrent;
use App\Models\BonusHunt;
use App\Models\BonusHuntGame;
use App\Models\BonusStage;
use App\Models\Bracket;
use App\Models\StageScore;
use App\Models\UserSetting;
use App\Observers\BattleViewerObserver;
use App\Observers\BonusBattleObserver;
use App\Observers\BonusBuyOrBattleObserver;
use App\Observers\SettingsObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        BattleViewer::observe(BattleViewerObserver::class);

        BonusBuy::observe(BonusBuyOrBattleObserver::class);
        BonusHunt::observe(BonusBuyOrBattleObserver::class);
        BonusBuyGame::observe(BonusBuyOrBattleObserver::class);
        BonusHuntGame::observe(BonusBuyOrBattleObserver::class);

        UserSetting::observe(SettingsObserver::class);

        BonusBattle::observe(BonusBattleObserver::class);
        BonusStage::observe(BonusBattleObserver::class);
        BonusConcurrent::observe(BonusBattleObserver::class);
        StageScore::observe(BonusBattleObserver::class);
        Bracket::observe(BonusBattleObserver::class);
    }
}
