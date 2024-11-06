<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\BonusBuyController;
use App\Http\Controllers\BonusBuyGameController;
use App\Http\Controllers\BonusHuntController;
use App\Http\Controllers\BonusHuntGameController;
use App\Http\Controllers\BonusListController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\GuessEntriesController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\SpinController;
use App\Http\Controllers\StreamAccountController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\UserSettingController;
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
    Route::patch('/settings', [UserSettingController::class, 'edit']);

    // Financial Reports Management
    Route::post('/deposits', [DepositController::class, 'store']);
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);

    // Banner Management
    Route::post('/banner', [BannerController::class, 'store']);
    Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

    // Bonus List Management
    Route::post('/set-latest-list', [UserSettingController::class, 'setGuessList']);
    Route::post('/close-bonus-list', [BonusListController::class, 'closeBonusList']);

    //viewer actions
    Route::post('/add-entry', [GuessEntriesController::class, 'postEntries']);

    Route::get('/stream-accounts', [StreamAccountController::class, 'index']);
    Route::post('/stream-accounts/new', [StreamAccountController::class, 'store']);
    Route::delete('/stream-accounts/{id}', [StreamAccountController::class, 'destroy']);

    Route::patch('/wheel-list', [SpinController::class, 'edit']);
});

// Public Routes (No Authentication Required)
Route::get('/stream', [StreamController::class, 'index']);
Route::get('/bonus-buy', [BonusBuyController::class, 'index']);
Route::get('/bonus-hunt', [BonusHuntController::class, 'index']);
Route::get('/bonus-hunt-all', [BonusHuntController::class, 'all']);
Route::get('/socials', [SocialController::class, 'index']);
Route::get('/settings', [UserSettingController::class, 'index']);
Route::get('/settings/{id}', [UserSettingController::class, 'show']);
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
Route::get('/get-latest-list', [BonusListController::class, 'getUrl']);


Route::get('/referral/{id}/{parent}/{child}', [ReferralController::class, 'registerReferral']);
Route::get('/ref-list/{id}', [ReferralController::class, 'getReferrals']);

// Viewer Action Routes
Route::get('/show-entries/{id}/{type}', [GuessEntriesController::class, 'showEntries']);
Route::get('/get-bonus-winner/{id}', [BonusListController::class, 'getBonusWinner']);