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

//display in OBS
Route::get('/bonus-list/{id}', function ($id) {
    return Inertia::render('OBS/BonusListObs', ['id' => $id]);
})->name('bonus-list');
Route::get('/stream-start/{id}', function ($id) {
    return Inertia::render('OBS/StreamStartObs', ['id' => $id]);
})->name('stream-start');
Route::get('/banners-obs/{id}', function ($id) {
    return Inertia::render('OBS/BannersObs', ['id' => $id]);
})->name('banners-obs');
Route::get('/bonus-winner-obs/{id}', function ($id) {
    return Inertia::render('OBS/BonusWinnerObs', ['id' => $id]);
})->name('bonus-winner-obs');
Route::get('/slot/{id}', function ($id) {
    return Inertia::render('OBS/SlotObs', ['id' => $id]);
})->name('slot');
Route::get('/yt-like-view-counter/{id}', function ($id) {
    return Inertia::render('OBS/YtLikeViewCounterObs', ['id' => $id]);
})->name('yt-like-view-counter');
Route::get('/bonus-battle-view/{id}', function ($id) {
    return Inertia::render('OBS/BonusBattleViewObs', ['id' => $id]);
})->name('bonus-battle-view');
Route::get('/bonus-battle-picker/{id}', function ($id) {
    return Inertia::render('OBS/BonusBattlePickerObs', ['id' => $id]);
})->name('bonus-battle-picker');
Route::get('/picker-wheel/{id}', function ($id) {
    return Inertia::render('OBS/PickerWheelObs', ['id' => $id]);
})->name('picker-wheel');
Route::get('/referrals/{id}', function ($id) {
    return Inertia::render('OBS/ReferralsObs', ['id' => $id]);
})->name('referrals');
Route::get('/schedule-view/{id}', function ($id) {
    return Inertia::render('OBS/ScheduleViewObs', ['id' => $id]);
})->name('schedule-view');

Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');



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
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Viewer/MainUser');
    })->name('dashboard');
    Route::get('/guess-list/{id}/{type}', [ViewerController::class, 'guessList'])->name('guess-list');
    Route::get('/streamers', [ViewerController::class, 'streamerList'])->name('streamer-list');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'streamer'
])->group(function () {
    Route::get('/streamdash', function () {
        return Inertia::render('StreamDash');
    })->name('streamdash');
    Route::get('/lists', function () {
        return Inertia::render('Bonuses');
    })->name('lists');
    Route::get('/bonus-battle', function () {
        return Inertia::render('BonusBattle');
    })->name('bonus-battle');
    Route::get('/settings', function () {
        return Inertia::render('Profile/Settings');
    })->name('settings');
    Route::get('/banners', function () {
        return Inertia::render('Profile/Banners');
    })->name('banners');
    Route::get('/report', function () {
        return Inertia::render('Report');
    })->name('report');
    Route::get('/stream-accounts', function () {
        return Inertia::render('Profile/StreamAccounts');
    })->name('stream-accounts');
    Route::get('/wheel-settings', function () {
        return Inertia::render('WheelSettings');
    })->name('wheel-settings');
    Route::get('/schedule', function () {
        return Inertia::render('SchedulePage');
    })->name('schedule');

    //sync games
    Route::get('/sync-games', [GameSyncController::class, 'syncGames']);
});
