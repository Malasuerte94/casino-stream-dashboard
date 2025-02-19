<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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


    /**
     * @param int $streamerId
     * @return Response
     */
    public function viewStreamer(int $streamerId): Response
    {
        $streamer = User::find($streamerId);
        if (!$streamer) {
            abort(404);
        }

        return Inertia::render('Viewer/ViewStreamer', ['steamerId' => $streamer->id]);
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function getSteamer($id): JsonResponse
    {
        $streamer = User::where('id', $id)->whereHas('role', function ($query) {
            $query->where('identifier', 'streamer');
        })->get();
        $streamer = UserResource::collection($streamer);
        return response()->json([
            'streamer' => $streamer[0],
        ]);
    }

    /**
     * Get Bonus Hunt history with associated games.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getBonusHuntHistory($id): JsonResponse
    {
        $bonusHunt = BonusHunt::where('user_id', $id)
            ->with([
                'bonusHuntGames.game' // Load full game details
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'bonusHunts' => $bonusHunt,
        ]);
    }


}
