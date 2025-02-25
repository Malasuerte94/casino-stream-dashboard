<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        return Inertia::render('Viewer/Streamers/StreamerList', ['streamers' => $streamers]);
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

        return Inertia::render('Viewer/GuessList', [
            'list' => $latestBonus,
            'games' => $gamesForBonus,
            'type' =>
                $type,
            'is_open' => (bool)$latestBonus->is_open
        ]);
    }


    /**
     * @param string $streamerName
     * @param string $section
     * @return Response
     */
    public function viewStreamer(string $streamerName, string $section = 'hunt'): Response
    {
        $streamer = User::where('name', $streamerName)->first();
        if (!$streamer) {
            abort(404);
        }
        return Inertia::render('Viewer/Streamer/ViewStreamer', ['steamerId' => $streamer->id, 'steamerName' => $streamerName, 'section' => $section]);
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

    /**
     * Get Bonus Hunt with associated games.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getBonusHunt($id): JsonResponse
    {
        $bonusHunt = BonusHunt::where('id', $id)
            ->with([
                'bonusHuntGames.game'
            ])
            ->first();

        return response()->json([
            'latestHunt' => $bonusHunt,
        ]);
    }

    public function getVerifyCode(Request $request): JsonResponse
    {
        $user = $request->user();
        if (empty($user->yt_code)) {
            $user->yt_code = Str::random(10);
        }
        $user->save();

        return response()->json([
            'code' => $user->yt_code,
        ]);
    }

    /**
     * Verify the code and sanitize the user input.
     *
     * @param string $userInput The user's YouTube name input.
     * @param string $code The verification code provided.
     * @return string
     */
    public function verifyCode(string $userInput, string $code): string
    {
        try {
            $user = User::where('yt_code', $code)->first();
            if ($user) {
                $sanitizedInput = trim(strip_tags($userInput));
                $user->yt_name = $sanitizedInput;
                $user->save();
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

}
