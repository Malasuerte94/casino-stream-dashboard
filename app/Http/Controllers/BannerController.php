<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BonusBuy;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $banners = $user->banners()->get();
        return response()->json([
            'banners' => $banners
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        $user = $request->user();

        $name = $request->name;
        $image = $request->file('image');

        if(!$name || !$image || !$user) {
            return response()->json([
                'error' => 'Missing name or image',
            ]);
        }

        $banner = $user->banners()->create([
            'name' => $name,
            'image' => $name
        ]);

        $imageUploaded = $banner->addMediaFromRequest('image')->toMediaCollection('images', 'cpanelpublic');

        if($image) {
            $banner->image = $imageUploaded->original_url;
            $banner->save();
            return response()->json([
                'uploaded' => $image,
            ]);
        } else {
            return response()->json([
                'error' => 'Cant upload your image',
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $banner = Banner::find($id);

        if(!$banner && $banner->user->id == $request->user()->id) {
            return response()->json([
                'message' => 'Banner not found',
            ]);
        }

        $banner->media()->delete();
        $banner->delete();

        return response()->json([
            'message' => 'Banner deleted',
        ]);
    }

    /**
    * Show from link
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $banners = $user->banners()->get();

        return response()->json([
            'banners' => $banners,
        ]);
    }


}
