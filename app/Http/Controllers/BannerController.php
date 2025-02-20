<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BannerAd;
use App\Models\BonusBuy;
use App\Models\Stream;
use App\Models\User;
use Exception;
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

        $imageUploaded = $banner->addMediaFromRequest('image')->toMediaCollection('banners', 'cpanelpublic');

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
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|url',
                'image' => 'required|image|max:2048',
            ]);

            $banner = $user->bannerAds()->create([
                'name' => $request->name,
                'url' => $request->url,
                'image' => '',
                'clicks' => 0,
            ]);

            $image = $banner->addMediaFromRequest('image')
                ->toMediaCollection('banner_ads', 'cpanelpublic');

            $banner->update(['image' => $image->getUrl()]);

            return response()->json([
                'message' => 'Banner ad added successfully!',
                'banner' => $banner,
                'image_url' => $image->getUrl(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to upload banner ad: ' . $e->getMessage()], 500);
        }


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

    /**
     * Show from link
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showAds(int $id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $banners = $user->bannerAds()->get();

        return response()->json([
            'bannersAds' => $banners,
        ]);
    }


    /**
     * Register a click on a banner.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function registerClick(int $id): JsonResponse
    {
        $banner = BannerAd::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }
        $banner->increment('clicks');
        return response()->json(['message' => 'Click registered', 'clicks' => $banner->clicks]);
    }


}
