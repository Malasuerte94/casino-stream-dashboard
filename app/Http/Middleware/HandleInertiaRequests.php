<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        //MODIFY AFTER YOUTUBE REGISTER
        return array_merge(parent::share($request), [
            'user_streamer' => fn () => $request->user()
                ? $request->user()->isStreamer()
                : null,
            'user.profile_photo_url' => fn () => $request->user() ? env('app_url').'/storage/'.$request->user()
                    ->profile_photo_path : null,
        ]);
    }
}
