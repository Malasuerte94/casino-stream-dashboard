<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updateProfilePicture(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|max:5024', // maximum size in kilobytes
        ]);

        $user = $request->user();

        $media = $user->addMediaFromRequest('photo')
            ->toMediaCollection('profile_pictures', 'cpanelpublic');

        if ($media) {
            $fullUrl = $media->getUrl();
            $relativeUrl = parse_url($fullUrl, PHP_URL_PATH);

            $relativeUrl = preg_replace('/^\/storage\//', '', $relativeUrl);

            $user->profile_photo_path = $relativeUrl;
            $user->save();

            return response()->json([
                'uploaded' => $relativeUrl,
            ]);
        } else {
            return response()->json([
                'error' => "Can't upload your image",
            ], 500);
        }
    }

    public function getProfilePicture($userId) {
        $user = User::find($userId);
        if ($user) {
            $media = $user->getFirstMedia('profile_pictures');
            if ($media) {
                $fullUrl = $media->getUrl();
                return response()->json([
                    'profile_picture' => $fullUrl,
                ]);
            }
        }
        return response()->json([
            'profile_picture' => null,
        ]);
    }
}