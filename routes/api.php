<?php

use App\Http\Controllers\BonusBuyController;
use App\Http\Controllers\BonusBuyGameController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\StreamController;
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


//patch una put pentru toate
//stream
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

//socials
Route::get('/socials', [SocialController::class, 'index']);
Route::post('/socials', [SocialController::class, 'store']);