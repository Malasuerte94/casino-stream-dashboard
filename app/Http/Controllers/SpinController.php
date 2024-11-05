<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SpinController extends Controller
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function index($id): JsonResponse
    {
        $userSettings = UserSetting::where('user_id', $id)->where('name', 'wheel_list')->first();
        if ($userSettings) {
            $wheelList = $userSettings->value;
            return response()->json($wheelList);
        }
        return response()->json([]);
    }

    public function edit(Request $request): JsonResponse
    {
        $userSettings = UserSetting::where('user_id', $request->user()->id)->where('name', 'wheel_list')->first();
        if ($userSettings) {
            $userSettings->update(['value' => $request->input('wheelList')]);
        } else {
            UserSetting::create([
                'user_id' => $request->user()->id,
                'name' => 'wheel_list',
                'value' => $request->input('wheelList'),
            ]);
        }
        return response()->json(['message' => 'Updated']);
    }
    /**
     * Trigger a spin for the specified wheel.
     */
    public function triggerSpin($id): String
    {
        Cache::put('spin_trigger_' . $id, true, 10); // Store spin trigger with wheel ID
        return 'wow';
    }

    /**
     * Check if a spin has been triggered for a specific wheel.
     */
    public function checkSpin($id): JsonResponse
    {
        $shouldSpin = Cache::pull('spin_trigger_' . $id, false); // Pull the flag and remove it
        return response()->json(['shouldSpin' => $shouldSpin]);
    }

    /**
     * Clear the spin trigger manually if needed.
     */
    public function clearSpin($id): JsonResponse
    {
        Cache::forget('spin_trigger_' . $id); // Remove specific wheel's trigger flag
        return response()->json(['message' => 'Spin trigger cleared for wheel ' . $id], 200);
    }

}
