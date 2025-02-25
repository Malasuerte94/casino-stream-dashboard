<?php

use App\Http\Controllers\GameSyncController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\StreamlabsController;
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
Route::post('/add-email-to-account', [SocialController::class, 'addRequiredEmail'])
    ->name('add-required-email-to-account');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
})->name('welcome');

// OBS PUBLIC ROUTES
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.ytname'
])->group(function () {
    Route::get('/account/verify', [SocialController::class, 'verifyYtAccount'])
        ->middleware('redirect.verified')
        ->name('account.verify');

    Route::prefix('user')->group(function () {
        Route::get('/streamers', [ViewerController::class, 'streamerList'])
            ->name('user.streamer-list');
        Route::get('/dashboard', function () {
            return Inertia::render('Viewer/MainUser');
        })->name('user.dashboard');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'streamer'
])->prefix('streamer')->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Streamer/StreamDash');
    })->name('streamer.dashboard');

    Route::get('/lists', function () {
        return Inertia::render('Streamer/BonusHuntBuy/Bonuses');
    })->name('streamer.lists');

    Route::get('/bonus-battle', function () {
        return Inertia::render('Streamer/BonusBattle/BonusBattle');
    })->name('streamer.bonus-battle');

    Route::get('/settings', function () {
        return Inertia::render('Streamer/Profile/Settings/Settings');
    })->name('streamer.settings');

    Route::get('/banners', function () {
        return Inertia::render('Streamer/Profile/Banners/Banners');
    })->name('streamer.banners');

    Route::get('/report', function () {
        return Inertia::render('Streamer/Report/Report');
    })->name('streamer.report');

    Route::get('/stream-accounts', function () {
        return Inertia::render('Streamer/Profile/StreamAccounts/StreamAccounts');
    })->name('streamer.stream-accounts');

    Route::get('/wheel-settings', function () {
        return Inertia::render('Streamer/Wheel/WheelSettings');
    })->name('streamer.wheel-settings');

    Route::get('/schedule', function () {
        return Inertia::render('Streamer/Schedule/SchedulePage');
    })->name('streamer.schedule');

    // ONLY STREAMER STREAMLABS
    Route::get('/streamlabs/authorize', [StreamlabsController::class, 'redirectToStreamlabs'])
        ->name('streamer.streamlabs.authorize');
    Route::get('/streamlabs/callback', [StreamlabsController::class, 'handleStreamlabsCallback'])
        ->name('streamer.streamlabs.callback');

    // sync games
    Route::get('/sync-games', [GameSyncController::class, 'syncGames']);
});

// PUBLIC STREAMER PAGE (Catch-All)
Route::get('/{user}/{section?}', [ViewerController::class, 'viewStreamer'])
    ->where('user', '[A-Za-z0-9_\-]+') // Adjust regex as needed for valid names
    ->where('section', '.*')
    ->name('view-streamer');