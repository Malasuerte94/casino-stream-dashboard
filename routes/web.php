<?php

use App\Http\Controllers\SocialController;
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

//display in obs
Route::get('/bonus-list/{id}', function ($id) {
    return Inertia::render('BonusList', ['id' => $id]);
})->name('bonus-list');
Route::get('/stream-start/{id}', function ($id) {
    return Inertia::render('StreamStart', ['id' => $id]);
})->name('stream-start');
Route::get('/banners-obs/{id}', function ($id) {
    return Inertia::render('BannersObs', ['id' => $id]);
})->name('banners-obs');
Route::get('/slot/{id}', function ($id) {
    return Inertia::render('Slot', ['id' => $id]);
})->name('slot');


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/streamdash', function () {
        return Inertia::render('StreamDash');
    })->name('streamdash');
    Route::get('/lists', function () {
        return Inertia::render('Bonuses');
    })->name('lists');
    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');
    Route::get('/banners', function () {
        return Inertia::render('Banners');
    })->name('banners');
    Route::get('/report', function () {
        return Inertia::render('Report');
    })->name('report');
});
