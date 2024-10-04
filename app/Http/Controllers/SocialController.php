<?php

namespace App\Http\Controllers;

use App\Models\Social;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{

    /**
     * @param $provider
     * @return RedirectResponse
     */
    public function redirect($provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from provider.
     *
     * @param string $provider
     * @return RedirectResponse|\Inertia\Response|Redirector|Application
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $social = Social::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            // If social user not found, create a new social record
            if (!$social) {
                $social = Social::create([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id,
                    'nickname' => $socialUser->nickname ?? trim(strip_tags($socialUser->name)),
                    'avatar' => $socialUser->avatar,
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]);
            }

            // Handle Google login (with email)
            if ($provider == 'google') {
                return $this->handleGoogleLogin($social, $socialUser->email);
            }

            // Handle YouTube login (no email)
            if ($provider == 'youtube') {
                return $this->handleYouTubeLogin($social);
            }

        } catch (Exception $e) {
            return redirect('/register')->withErrors(['error' => 'Login failed. Please try again.']);
        }
    }

    /**
     * Add email after YouTube login.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector|Application
     */
    public function addRequiredEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'social' => 'required|exists:socials,id',
        ]);

        $social = Social::findOrFail($request->input('social'));
        $user = User::firstOrCreate(
            ['email' => $request->input('email')],
            [
                'name' => $social->nickname,
                'password' => bcrypt(Str::random(10)),
                'profile_photo_path' => $social->avatar,
            ]
        );

        $social->update(['user_id' => $user->id]);
        Auth::login($user);

        return redirect('/streamdash');
    }

    /**
     * Handle Google login/register process.
     *
     * @param Social $social
     * @param string $email
     * @return RedirectResponse|Redirector|Application
     */
    private function handleGoogleLogin(Social $social, string $email)
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $social->nickname,
                'password' => bcrypt(Str::random(10)),
                'profile_photo_path' => $social->avatar,
            ]
        );

        $social->update(['user_id' => $user->id]);
        Auth::login($user);

        return redirect('/streamdash');
    }

    /**
     * Handle YouTube login process (no email, request email).
     *
     * @param Social $social
     * @return \Inertia\Response
     */
    private function handleYouTubeLogin(Social $social): \Inertia\Response
    {
        // If no associated user, redirect to AddRequiredEmail
        if (!$social->user) {
            return Inertia::render('Auth/AddRequiredEmail', ['socialId' => $social->id]);
        }

        Auth::login($social->user);
        return redirect('/streamdash');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //receive name, slug, icon, upload the icon and save the path in the db

        $request->validate([
            'file' => 'required|mimes:png,svg,jpeg|max:2048',
        ]);
    
        $fileName = time().'.'.$request->file->extension();  
     
        $request->file->move(public_path('uploads'), $fileName);

        $social = new Social();
        $social->name = $request->sName;
        $social->slug = $request->sSlug;
        $social->icon = $fileName;
        $social->save();

        return response()->json($social);
        
    }

}
