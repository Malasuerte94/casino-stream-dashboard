<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $settings = $user->userSettings;

        if ($settings->isEmpty()) { // Check if $settings does not exist
            $settings = $user->userSettings()->create([
                'name' => 'bonus_list',
                'value' => 'hunt',
                'user_id' => $user->id,
            ]);
        }

        // Use pluck to create a key-value pair array from the 'name' and 'value' columns
        $settingsArray = $user->userSettings->pluck('value', 'name')->all();

        return response()->json([
            'settings' => $settingsArray,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($id);
        $settings = $user->userSettings;

        if ($settings->isEmpty()) { // Check if $settings does not exist
            $settings = $user->userSettings()->create([
                'name' => 'bonus_list',
                'value' => 'hunt',
                'user_id' => $user->id,
            ]);
        }

        // Use pluck to create a key-value pair array from the 'name' and 'value' columns
        $settingsArray = $user->userSettings->pluck('value', 'name')->all();

        return response()->json([
            'settings' => $settingsArray,
        ]);
    }

    /**
     * edit the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        $inputSettings = $request->get('settings');

        foreach ($inputSettings as $name => $value) {
            $setting = $user->userSettings()->where('name', $name)->first();

            if ($setting) { // If the setting exists, update it
                $setting->update(['value' => $value]);
            } else { // If the setting does not exist, create it
                $user->userSettings()->create([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }

        $settingsArray = $user->userSettings->pluck('value', 'name')->all();

        return response()->json([
            'settings' => $settingsArray,
        ]);
    }


}
