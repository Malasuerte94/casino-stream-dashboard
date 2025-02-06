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
        $this->checkIfSettingExists($user);

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
        $this->checkIfSettingExists($user);

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
     * edit the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        $inputSettings = $request->get('setting_name');
        $inputSettingsValue = $request->get('setting_value');
        $setting = $user->userSettings()->where('name', $inputSettings)->first();

        if ($setting) {
            $setting->update(['value' => $inputSettingsValue]);
        } else {
             $user->userSettings()->create([
                'name' => $inputSettings,
                'value' => $inputSettingsValue,
            ]);
        }

        return response()->json([
            'settings' => 'Saved',
        ]);
    }



    /**
     * Retrieve a specific user setting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSetting(Request $request): JsonResponse
    {
        $user = $request->user();
        if(!$user){
            $user = User::find($request->query('user_id'));
        }
        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        $this->checkIfSettingExists($user);

        $settingName = $request->query('setting_name'); // Get setting name from request query

        $setting = $user->userSettings()->where('name', $settingName)->first();

        if (!$setting) {
            return response()->json([
                'error' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'setting_name' => $settingName,
            'setting_value' => $setting->value,
        ]);
    }


    /**
     * Retrieve a specific user setting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSettingPublic(Request $request): JsonResponse
    {
        $user = User::find($request->query('user_id'));
        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        $this->checkIfSettingExists($user);

        $settingName = $request->query('setting_name'); // Get setting name from request query

        $setting = $user->userSettings()->where('name', $settingName)->first();

        if (!$setting) {
            return response()->json([
                'error' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'setting_name' => $settingName,
            'setting_value' => $setting->value,
        ]);
    }


    private function checkIfSettingExists(User $user): void
    {
        $requiredSettings = [
            'bonus_list' => 'hunt',
            'obs_bonus_list' => '{"tableBgColor":"rgba(0, 0, 0, 0.39)","borderColor":"rgb(0, 0, 0)","borderEnabled":true,"borderWidth":2,"showIcon":true,"currency":"LEI","headerBgColor":"rgb(255, 167, 39)","headerFontSize":15,"headerFontColor":"rgb(0, 0, 0)","headerCellBgColor":"rgba(255,255,255,0.85)","progressBarLow":"rgb(255, 245, 156)","progressBarHigh":"rgb(230, 74, 25)","progressBarHeight":4,"subheaderBgColor":"rgb(0, 0, 0)","subheaderFontColor":"rgb(255, 255, 255)","subheaderFontSize":14,"tableHeaderFontSize":16,"tableHeaderFontColor":"rgb(255, 255, 255)","tableHeaderBgColor":"rgb(255, 167, 39)","tableBodyFontColor":"rgb(255, 255, 255)","tableDividerColor":"rgba(0, 0, 0, 0.14)","tableBodyFontSize":12,"obs_bonus_list":{"tableBgColor":"#ededed","borderColor":"#000","borderEnabled":true,"borderWidth":4,"showIcon":true,"currency":"LEI","headerBgColor":"#ffb700","headerFontSize":16,"headerFontColor":"#000000","headerCellBgColor":"rgba(255,255,255,0.85)","progressBarLow":"#ffb700","progressBarHigh":"#ff0000","progressBarHeight":4,"subheaderBgColor":"#ddd","subheaderFontColor":"#5a0404","subheaderFontSize":14,"tableHeaderFontSize":16,"tableHeaderFontColor":"#c60a0a","tableHeaderBgColor":"#249382","tableBodyFontColor":"#a55353","tableDividerColor":"#ff0000","tableBodyFontSize":12},"tableCurrentGameColor":"rgba(251, 140, 0, 0.36)"}',
            'obs_schedule' => '{"cellBg":"rgb(7, 28, 10)","cellBgShort":"rgb(13, 18, 5)","cellBgActive":"rgb(251, 140, 0)","cellFontColor":"#ffffff","cellTitleFontSize":22,"cellSubtitleFontSize":20}',
            'obs_bonus_battle' => '{"tableBgColor":"rgba(0, 0, 0, 0.67)","borderColor":"rgb(33, 33, 33)","borderEnabled":true,"borderWidth":4,"currency":"LEI","headerBgColor":"rgb(246, 124, 1)","headerFontSize":13,"headerFontColor":"rgb(33, 33, 33)","subheaderBgColorRound":"rgb(255, 255, 255)","subheaderFontColorRound":"rgb(246, 124, 1)","tableParticipantFontColor":"rgb(255, 255, 255)","tableParticipantBgColor":"rgb(33, 33, 33)","subheaderBgColor":"rgb(33, 33, 33)","subheaderFontColor":"rgb(255, 255, 255)","subheaderFontSize":15,"tableParticipantFontSize":14,"tableScoresBgColor":"rgb(33, 33, 33)","tableScoresLabelBgColor":"rgb(230, 74, 25)","tableScoresFontColor":"rgb(255, 255, 255)","tableHistoryBgOddColor":"rgb(75, 75, 75)","tableBodyFontSize":11,"tableHistoryBgEvenColor":"rgb(53, 53, 53)","tableScoresFontSize":12, "tableHistoryEnable":true}'
        ];
        foreach ($requiredSettings as $name => $defaultValue) {
            $settingExists = $user->userSettings()->where('name', $name)->exists();
            if (!$settingExists) {
                $user->userSettings()->create([
                    'name' => $name,
                    'value' => $defaultValue,
                    'user_id' => $user->id,
                ]);
            }
        }
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


    /**
     * Save Discord Webhook for Hunt & Battle Announcer.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function saveDiscordWebhookHuntBattleAnnouncer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'discordWebhook' => 'required|string|url',
        ]);

        $userId = $request->user()->id;
        UserSetting::updateOrCreate(
            ['user_id' => $userId, 'name' => 'discord_wbh_hunt_battle'],
            ['value' => $validated['discordWebhook']]
        );

        return response()->json(['message' => 'Webhook saved successfully.'], 200);
    }

}
