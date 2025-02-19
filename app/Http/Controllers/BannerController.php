<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BannerAd;
use App\Models\BonusBuy;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $banners = $user->banners()->get();
        return response()->json([
            'banners' => $banners
        ]);
    }

    /**
     * Get all banner ads for the authenticated user.
     */
    public function indexBannerAds(Request $request): JsonResponse
    {
        $user = $request->user();
        $bannerAds = BannerAd::where('user_id', $user->id)->get();

        return response()->json([
            'bannerAds' => $bannerAds,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $name = $request->name;
        $image = $request->file('image');

        if (!$name || !$image || !$user) {
            return response()->json([
                'error' => 'Missing name or image',
            ]);
        }

        $banner = $user->banners()->create([
            'name' => $name,
            'image' => $name
        ]);

        $imageUploaded = $banner->addMediaFromRequest('image')->toMediaCollection('images', 'cpanelpublic');

        if ($image) {
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
     * Store a new banner ad.
     */
    public function storeBannerAd(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'required|url',
        ]);

        // Upload image
        $imagePath = $request->file('image')->store('banner_ads', 'public');

        // Create banner ad
        $bannerAd = BannerAd::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'image' => "/storage/" . $imagePath,
            'url' => $request->url,
            'clicks' => 0,
        ]);

        return response()->json([
            'message' => 'Banner ad added successfully!',
            'bannerAd' => $bannerAd,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (!$banner && $banner->user->id == $request->user()->id) {
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
     * Remove a banner ad.
     */
    public function destroyBannerAd(Request $request, int $id): JsonResponse
    {
        $bannerAd = BannerAd::find($id);

        if (!$bannerAd || $bannerAd->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Banner ad not found or unauthorized',
            ], 403);
        }

        // Delete associated image
        if ($bannerAd->image) {
            unlink(public_path($bannerAd->image));
        }

        $bannerAd->delete();

        return response()->json([
            'message' => 'Banner ad deleted successfully',
        ]);
    }

    /**
     * Show from link
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $banners = $user->banners()->get();

        return response()->json([
            'banners' => $banners,
        ]);
    }


}
