<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSettingController extends Controller
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
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
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
     * @return JsonResponse
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


    /**
     * @param Request $request
     */
    public function setGuessList(Request $request): void
    {
        $request->validate([
            'is_open' => 'required',
            'list_id' => 'required',
            'type' => 'required|string'
        ]);

        $isOpen = $request->input('is_open') === 'true';
        $listId = $request->input('list_id');
        $type = $request->input('type');

        if ($type === 'buy') {
            $latestBonus = BonusBuy::find($listId);
        } else {
            $latestBonus = BonusHunt::find($listId);
        }

        $latestBonus->is_open = $isOpen === true;
        $latestBonus->save();
    }


    /**
     * Save Discord Webhook for Schedule Announcer.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function saveDiscordWebhookScheduleAnnouncer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'discordWebhook' => 'required|string|url',
        ]);

        $userId = $request->user()->id;
        UserSetting::updateOrCreate(
            ['user_id' => $userId, 'name' => 'discord_wbh_schedule'],
            ['value' => $validated['discordWebhook']]
        );

        return response()->json(['message' => 'Webhook saved successfully.'], 200);
    }

}
