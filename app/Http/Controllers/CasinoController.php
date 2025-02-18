<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CasinoController extends Controller
{
    public function getCasinos(): JsonResponse
    {
        $casinos = Casino::all();
        return response()->json([
            'casinos' => $casinos
        ]);
    }

    public function addCasino(Request $request): JsonResponse
    {
        // Ensure request is multipart/form-data
        if (!$request->isMethod('post') || !$request->hasFile('image')) {
            return response()->json([
                'error' => 'Invalid request or missing image file',
            ], 400);
        }

        // Extract request data
        $name = $request->input('name');
        $url = $request->input('url');
        $image = $request->file('image');

        // Ensure name and image are present
        if (!$name || !$image) {
            return response()->json([
                'error' => 'Missing name or image',
            ], 400);
        }

        // Create casino record
        $casino = Casino::create([
            'name' => $name,
            'logo' => '',
            'url' => $url,
        ]);

        // Upload image to media collection
        $imageUploaded = $casino->addMediaFromRequest('image')->toMediaCollection('casino-images', 'cpanelpublic');

        // Update the casino image field with the actual uploaded image URL
        $casino->logo = $imageUploaded->getFullUrl();
        $casino->save();

        return response()->json([
            'success' => true,
            'message' => 'Casino added successfully',
            'casino' => $casino,
        ]);
    }
}
