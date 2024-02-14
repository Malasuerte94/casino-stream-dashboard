<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\BonusBuyController;
use App\Http\Controllers\BonusBuyGameController;
use App\Http\Controllers\BonusHuntController;
use App\Http\Controllers\BonusHuntGameController;
use App\Http\Controllers\BonusListController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\SocialController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/stream', [StreamController::class, 'index']);
Route::post('/stream/new', [StreamController::class, 'store']);
Route::patch('/stream/update', [StreamController::class, 'edit']);

//bonus buy
Route::get('/bonus-buy', [BonusBuyController::class, 'index']);
Route::post('/bonus-buy', [BonusBuyController::class, 'store']);
Route::patch('/bonus-buy', [BonusBuyController::class, 'edit']);

//bonus buy games
Route::put('/bonus-buy-games', [BonusBuyGameController::class, 'update']);
Route::post('/bonus-buy-games', [BonusBuyGameController::class, 'store']);
Route::delete('/bonus-buy-games/{id}', [BonusBuyGameController::class, 'destroy']);

//bonus hunt
Route::get('/bonus-hunt', [BonusHuntController::class, 'index']);
Route::get('/bonus-hunt-all', [BonusHuntController::class, 'all']);
Route::post('/bonus-hunt', [BonusHuntController::class, 'store']);
Route::patch('/bonus-hunt', [BonusHuntController::class, 'edit']);

//bonus hunt games
Route::put('/bonus-hunt-games', [BonusHuntGameController::class, 'update']);
Route::post('/bonus-hunt-games', [BonusHuntGameController::class, 'store']);
Route::delete('/bonus-hunt-games/{id}', [BonusHuntGameController::class, 'destroy']);

//socials
Route::get('/socials', [SocialController::class, 'index']);
Route::post('/socials', [SocialController::class, 'store']);

//settings
Route::get('/settings', [UserSettingController::class, 'index']);
Route::get('/settings/{id}', [UserSettingController::class, 'show']);
Route::patch('/settings', [UserSettingController::class, 'edit']);

//REPORT
Route::get('/deposits', [DepositController::class, 'index']);
Route::get('/deposits/{id}', [DepositController::class, 'show']);
Route::get('/withdrawals', [WithdrawalController::class, 'index']);
Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show']);

Route::post('/deposits', [DepositController::class, 'store']);
Route::post('/withdrawals', [WithdrawalController::class, 'store']);

//banners
Route::get('/banner', [BannerController::class, 'index']);
Route::post('/banner', [BannerController::class, 'store']);
Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

//LIST PUBLIC
Route::get('/bonus-list/{id}', [BonusListController::class, 'index']);
Route::get('/stream/{id}', [StreamController::class, 'show']);
Route::get('/banners/{id}', [BannerController::class, 'show']);
