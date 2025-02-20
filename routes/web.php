<?php

use App\Http\Controllers\GameSyncController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ViewerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/auth/{provider}/redirect', [SocialController::class, 'redirect'])
    ->where('provider', 'google|youtube');
Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])
    ->where('provider', 'google|youtube');
Route::post('/add-email-to-account', [SocialController::class, 'addRequiredEmail'])->name('add-required-email-to-account');

//display in OBS - PUBLIC
Route::get('/bonus-list/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/BonusListObs', ['id' => $id]);
})->name('bonus-list');
Route::get('/stream-start/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/StreamStartObs', ['id' => $id]);
})->name('stream-start');
Route::get('/banners-obs/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/BannersObs', ['id' => $id]);
})->name('banners-obs');
Route::get('/bonus-winner-obs/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/BonusWinnerObs', ['id' => $id]);
})->name('bonus-winner-obs');
Route::get('/slot/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/SlotObs', ['id' => $id]);
})->name('slot');
Route::get('/yt-like-view-counter/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/YtLikeViewCounterObs', ['id' => $id]);
})->name('yt-like-view-counter');
Route::get('/bonus-battle-view/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/BonusBattleViewObs', ['id' => $id]);
})->name('bonus-battle-view');
Route::get('/bonus-battle-picker/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/BonusBattlePickerObs', ['id' => $id]);
})->name('bonus-battle-picker');
Route::get('/picker-wheel/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/PickerWheelObs', ['id' => $id]);
})->name('picker-wheel');
Route::get('/referrals/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/ReferralsObs', ['id' => $id]);
})->name('referrals');
Route::get('/schedule-view/{id}', function ($id) {
    return Inertia::render('Streamer/OBS/ScheduleViewObs', ['id' => $id]);
})->name('schedule-view');

Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');
//display in OBS - PUBLIC


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.ytname'
])->group(function () {
    Route::get('/account/verify', [SocialController::class, 'verifyYtAccount'])
        ->middleware('redirect.verified')
        ->name('account.verify');
    Route::get('/streamers', [ViewerController::class, 'streamerList'])->name('streamer-list');
    Route::get('/dashboard', function () {
        return Inertia::render('Viewer/MainUser');
    })->name('dashboard');

    Route::get('/guess-list/{id}/{type}', [ViewerController::class, 'guessList'])->name('guess-list');
    Route::get('/streamer/{id}', [ViewerController::class, 'viewStreamer'])->name('view-streamer');
    Route::get('/streamers', [ViewerController::class, 'streamerList'])->name('streamer-list');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'streamer'
])->group(function () {
    Route::get('/streamdash', function () {
        return Inertia::render('Streamer/StreamDash');
    })->name('streamdash');
    Route::get('/lists', function () {
        return Inertia::render('Streamer/BonusHuntBuy/Bonuses');
    })->name('lists');
    Route::get('/bonus-battle', function () {
        return Inertia::render('Streamer/BonusBattle/BonusBattle');
    })->name('bonus-battle');
    Route::get('/settings', function () {
        return Inertia::render('Streamer/Profile/Settings/Settings');
    })->name('settings');
    Route::get('/banners', function () {
        return Inertia::render('Streamer/Profile/Banners/Banners');
    })->name('banners');
    Route::get('/report', function () {
        return Inertia::render('Streamer/Report/Report');
    })->name('report');
    Route::get('/stream-accounts', function () {
        return Inertia::render('Streamer/Profile/StreamAccounts/StreamAccounts');
    })->name('stream-accounts');
    Route::get('/wheel-settings', function () {
        return Inertia::render('Streamer/Wheel/WheelSettings');
    })->name('wheel-settings');
    Route::get('/schedule', function () {
        return Inertia::render('Streamer/Schedule/SchedulePage');
    })->name('schedule');

    //sync games
    Route::get('/sync-games', [GameSyncController::class, 'syncGames']);
});
