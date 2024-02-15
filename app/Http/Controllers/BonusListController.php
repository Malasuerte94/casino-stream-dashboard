<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BonusListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function index(int $id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $bonusListSetting = $user->userSettings->where('name', 'bonus_list')->first()->value ?? 'hunt';

        $latestStream = $user
            ->streams()
            ->latest()
            ->first();

        if ($bonusListSetting === 'buy') {
            $latestBonus = $latestStream
                ->bonusBuys()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusBuyGames;
        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusHuntGames;
        }

        return response()->json([
            'list-id' => $latestBonus->id,
            'bonusListGames' => $gamesForBonus,
            'bonusList' => $latestBonus,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUrl(Request $request): JsonResponse
    {
        $user = $request->user();

        $bonusListSetting = $user->userSettings->where('name', 'bonus_list')->first()->value ?? 'hunt';

        $latestStream = $user
            ->streams()
            ->latest()
            ->first();

        if ($bonusListSetting === 'buy') {
            $latestBonus = $latestStream
                ->bonusBuys()
                ->latest()
                ->first();

        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
        }

        return response()->json([
            'list-id' => $latestBonus->id,
            'bonusList' => $latestBonus,
        ]);
    }
}
