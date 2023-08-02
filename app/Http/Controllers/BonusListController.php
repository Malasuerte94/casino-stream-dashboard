<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BonusListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $id): \Illuminate\Http\JsonResponse
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
            $gamesForBonus = $latestBonus->bonusBuyGame;
        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusHuntGame;
        }

        return response()->json([
            'bonusListGames' => $gamesForBonus,
            'bonusList' => $latestBonus,
        ]);
    }
}
