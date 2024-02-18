<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ViewerController extends Controller
{
    /**
     * @return Response
     */
    public function streamerList(): Response
    {
        $streamers = User::whereHas('role', function ($query) {
            $query->where('identifier', 'streamer');
        })->get();
        $streamers = UserResource::collection($streamers);
        return Inertia::render('Viewer/StreamerList', ['streamers' => $streamers]);
    }

    /**
     * @param int $bonusListId
     * @param string $type
     * @return Response
     */
    public function guessList(int $bonusListId, string $type): Response
    {

        if ($type === 'buy') {
            $latestBonus = BonusBuy::find($bonusListId);
            $gamesForBonus = $latestBonus->bonusBuyGames;
        } else {
            $latestBonus = BonusHunt::find($bonusListId);
            $gamesForBonus = $latestBonus->bonusHuntGames;
        }

        return Inertia::render('Viewer/GuessList', ['list' => $latestBonus, 'games' => $gamesForBonus, 'type' =>
            $type, 'is_open' => (bool)$latestBonus->is_open]);
    }
}
