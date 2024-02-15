<?php

namespace App\Http\Controllers;

use App\Models\Social;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
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
     * @param $provider
     * @return Application|RedirectResponse|Redirector|\Inertia\Response
     */
    public function callback($provider): \Inertia\Response|Redirector|Application|RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            $social = Social::where('provider', $provider)->where('provider_id', $socialUser->getId())->first();
            if (!$social) {
                $social = Social::create([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id,
                    'nickname' => $socialUser->nickname,
                    'avatar' => $socialUser->avatar,
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]);
            }
            if ($social->user) {
                auth()->login($social->user);
                return redirect('/streamdash');
            } else {
                return Inertia::render('Auth/AddRequiredEmail', ['socialId' => $social->id]);
            }
        } catch (Exception $e) {
            return redirect('/register');
        }

    }

    /**
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    public function addRequiredEmail(Request $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'social' => 'required',
                'remember' => 'required'
            ]);

            $social = Social::findOrFail($request->input('social'));
            $user = User::where('email', $request->input('email'))->first();

            if($user) {
                return redirect('/register');
            }

            $user = User::create([
                'email' => $request->input('email'),
                'name'=> $social->nickname,
                'password' => bcrypt(Str::random(10)),
                'profile_photo_path' => $social->avatar,
            ]);

            $social->update(['user_id' => $user->id]);

            auth()->login($user);

            return redirect('streamdash');
        } catch (Exception $e) {
            return redirect('/register');
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $socials = Social::all();
        return response()->json($socials);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Social  $social
     * @return Response
     */
    public function show(Social $social)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Social  $social
     * @return Response
     */
    public function edit(Social $social)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Models\Social  $social
     * @return Response
     */
    public function update(Request $request, Social $social)
    {
        //
    }

}
