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
            $user->update(['profile_photo_path' => $media->getUrl()]);
            return response()->json([
                'uploaded' => $user->profile_photo_path,
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
            $media = $user->getMedia('profile_pictures')->sortByDesc('created_at')->first();
            if ($media) {
                return response()->json([
                    'profile_picture' => $media->getUrl(),
                ]);
            }
        }
        return response()->json([
            'profile_picture' => null,
        ]);
    }
}