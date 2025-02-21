<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\BattleViewerController;
use App\Http\Controllers\BonusBattleController;
use App\Http\Controllers\BonusBuyController;
use App\Http\Controllers\BonusBuyGameController;
use App\Http\Controllers\BonusHuntController;
use App\Http\Controllers\BonusHuntGameController;
use App\Http\Controllers\BonusListController;
use App\Http\Controllers\CasinoController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GuessEntriesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\SpinController;
use App\Http\Controllers\StreamAccountController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\ViewerController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/profile-picture/{id}', [ProfileController::class, 'getProfilePicture']);
    Route::post('/user/profile-picture', [ProfileController::class, 'updateProfilePicture']);

    // Stream Management
    Route::post('/stream/new', [StreamController::class, 'store']);
    Route::patch('/stream/update', [StreamController::class, 'edit']);

    // Bonus Buy Management
    Route::post('/bonus-buy', [BonusBuyController::class, 'store']);
    Route::patch('/bonus-buy', [BonusBuyController::class, 'edit']);

    // Bonus Buy Games Management
    Route::put('/bonus-buy-games', [BonusBuyGameController::class, 'update']);
    Route::post('/bonus-buy-games', [BonusBuyGameController::class, 'store']);
    Route::delete('/bonus-buy-games/{id}', [BonusBuyGameController::class, 'destroy']);

    // Bonus Hunt Management
    Route::post('/bonus-hunt', [BonusHuntController::class, 'store']);
    Route::patch('/bonus-hunt', [BonusHuntController::class, 'edit']);

    // Bonus Hunt Games Management
    Route::put('/bonus-hunt-games', [BonusHuntGameController::class, 'update']);
    Route::post('/bonus-hunt-games', [BonusHuntGameController::class, 'store']);
    Route::delete('/bonus-hunt-games/{id}', [BonusHuntGameController::class, 'destroy']);

    // Social Management
    Route::post('/socials', [SocialController::class, 'store']);

    // Settings Management
    Route::get('/get-setting', [UserSettingController::class, 'getSetting']);

    Route::patch('/settings', [UserSettingController::class, 'edit']);
    Route::post('/settings/save', [UserSettingController::class, 'saveSettings']);

    Route::post('/user-settings/save-discord-webhook', [UserSettingController::class, 'saveDiscordWebhookScheduleAnnouncer']);
    Route::post('/user-settings/save-discord-webhook/hunt-buy-battle', [UserSettingController::class, 'saveDiscordWebhookHuntBattleAnnouncer']);

    // Financial Reports Management
    Route::post('/deposits', [DepositController::class, 'store']);
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);

    // Banner Management
    Route::post('/banner', [BannerController::class, 'store']);
    Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

    Route::post('/banner-ads', [BannerController::class, 'storeBannerAd']);
    Route::delete('/banner-ads/{id}', [BannerController::class, 'destroyBannerAd']);
    Route::get('/banner-ads', [BannerController::class, 'indexBannerAds']);


    // Bonus List Management
    Route::post('/set-latest-list', [UserSettingController::class, 'setGuessList']);
    Route::post('/close-bonus-list', [BonusListController::class, 'closeBonusList']);

    //viewer actions

    Route::get('/stream-accounts', [StreamAccountController::class, 'index']);
    Route::post('/stream-accounts/new', [StreamAccountController::class, 'store']);
    Route::delete('/stream-accounts/{id}', [StreamAccountController::class, 'destroy']);

    Route::patch('/wheel-list', [SpinController::class, 'edit']);

    Route::prefix('bonus-battles')->group(function () {
        Route::post('/', [BonusBattleController::class, 'store']);
        Route::get('/active', [BonusBattleController::class, 'getActiveBattle']);
        Route::post('/finish-round', [BonusBattleController::class, 'finishRound']);
        Route::post('/end-battle', [BonusBattleController::class, 'endBattle']);
        Route::post('/end-battle-forced', [BonusBattleController::class, 'endBattleForced']);
        Route::post('/add-score', [BonusBattleController::class, 'addScore']);
        Route::put('/edit-concurrent', [BonusBattleController::class, 'editConcurrent']);
        Route::delete('/delete-score/{id}', [BonusBattleController::class, 'deleteScore']);
        Route::get('/battle-viewers', [BattleViewerController::class, 'getBattleViewers']);
        Route::patch('/battle-viewers/{id}', [BattleViewerController::class, 'updateBattleViewer']);
        Route::post('/battle-viewers/remove-all', [BattleViewerController::class, 'clearBattleViewers']);
    });


    Route::prefix('schedule')->group(function () {
        Route::get('latest', [ScheduleController::class, 'getLatest']);
        Route::get('all', [ScheduleController::class, 'getAll']);
        Route::get('{schedule}', [ScheduleController::class, 'getSchedule']);
        Route::patch('{schedule_day}/edit', [ScheduleController::class, 'editDay']);
        Route::post('/', [ScheduleController::class, 'createSchedule']);
        Route::patch('{schedule_day}/toggle-status', [ScheduleController::class, 'toggleStatus']);
        Route::post('/{id}/announce', [DiscordController::class, 'sendScheduleMessage']);
    });


    Route::get('/test', [BonusBattleController::class, 'test']);

    Route::prefix('viewer')->group(function () {
        Route::get('get-yt-code', [ViewerController::class, 'getVerifyCode']);
        Route::get('get-streamer/{id}', [ViewerController::class, 'getSteamer']);
        Route::get('get-bh-history/{id}', [ViewerController::class, 'getBonusHuntHistory']);
        Route::get('get-bh/{id}', [ViewerController::class, 'getBonusHunt']);

        Route::get('get-bb-history/{id}', [BonusBattleController::class, 'getBonusBattleHistory']);
        Route::get('get-bb/{id}', [BonusBattleController::class, 'getSingleBonusBattleInfo']);

        Route::post('add-prediction', [GuessEntriesController::class, 'postPrediction']);
        Route::get('get-prediction/{id}', [GuessEntriesController::class, 'getPrediction']);
        Route::get('get-predictions/{id}', [GuessEntriesController::class, 'getPredictions']);
    });
});

// Public Routes (No Authentication Required)
Route::get('/stream', [StreamController::class, 'index']);
Route::get('/bonus-buy', [BonusBuyController::class, 'index']);
Route::get('/bonus-buy-all', [BonusBuyController::class, 'all']);

Route::get('/bonus-hunt', [BonusHuntController::class, 'index']);
Route::get('/bonus-hunt-all', [BonusHuntController::class, 'all']);

Route::get('/socials', [SocialController::class, 'index']);
Route::get('/settings', [UserSettingController::class, 'index']);
Route::get('/settings/{id}', [UserSettingController::class, 'show']);
Route::get('/get-setting-public', [UserSettingController::class, 'getSettingPublic']);
Route::get('/deposits', [DepositController::class, 'index']);
Route::get('/deposits/{id}', [DepositController::class, 'show']);
Route::get('/withdrawals', [WithdrawalController::class, 'index']);
Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show']);
Route::get('/banner', [BannerController::class, 'index']);

Route::get('/wheel-list/{id}', [SpinController::class, 'index']);
Route::get('/spin/{id}', [SpinController::class, 'triggerSpin']);
Route::get('/spin/check/{id}', [SpinController::class, 'checkSpin']);
Route::post('/spin/clear/{id}', [SpinController::class, 'clearSpin']);

Route::get('/youtube-link/{id}', [StreamController::class, 'getYoutubeLink']);
Route::post('/get-youtube-data', [StreamController::class, 'getYoutubeData']);

Route::get('/bonus-list/{id}', [BonusListController::class, 'index']);
Route::get('/stream/{id}', [StreamController::class, 'show']);
Route::get('/banners/{id}', [BannerController::class, 'show']);
Route::get('/banners-ads/{id}', [BannerController::class, 'showAds']);
Route::post('/banner-ads/{id}/click', [BannerController::class, 'registerClick']);
Route::get('/get-latest-list', [BonusListController::class, 'getUrl']);

Route::get('/ref-list/{id}', [ReferralController::class, 'getReferrals']);

// Viewer Action Routes
Route::get('/show-entries/{id}/{type}', [GuessEntriesController::class, 'showEntries']);
Route::get('/get-bonus-winner/{id}', [BonusListController::class, 'getBonusWinner']);

Route::get('/bonus-battle-info/{id}', [BonusBattleController::class, 'getBonusBattleInfo']);

//schedule
Route::get('/schedule/weekly/{id}', [ScheduleController::class, 'getWeeklySchedule']);

//get-all-games
Route::apiResource('games', GameController::class);

//get-all-users-list
Route::get('/battle-viewers-public/{id}', [BattleViewerController::class, 'getBattleViewersPublic']);

Route::get('/get-casinos', [CasinoController::class, 'getCasinos']);
Route::post('/add-casino', [CasinoController::class, 'addCasino']);

//viewer actions - YT-BOT
Route::get('/verify-yt-code/{user}/{code}', [ViewerController::class, 'verifyCode']);
//register for bonus battle picker
Route::get('/add-bb-viewer/{username}/{game}/{creatorId}',[BattleViewerController::class, 'addBattleViewer']);
//add refferal
Route::get('/referral/{id}/{parent}/{child}', [ReferralController::class, 'registerReferral']);