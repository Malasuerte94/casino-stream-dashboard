<?php

namespace App\Http\Controllers;

use App\Models\BonusHunt;
use App\Models\BonusHuntGame;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BonusHuntGameController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $bonusHunt = $request->bonusHuntId;
        $bonusHunt = BonusHunt::find($bonusHunt);
        if(!$bonusHunt) {
            return response()->json([
                'message' => 'Bonus Buy not found',
            ]);
        }

        $bonusHuntGame = new BonusHuntGame();
        $bonusHuntGame->bonusHunt()->associate($bonusHunt);
        $bonusHuntGame->save();
        return response()->json([
            'message' => 'Bonus Hunt Game created',
            'bonusHuntGame' => $bonusHuntGame,
        ]);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $totalResult = 0;

        foreach ($request->games as $game) {
            if (!$game['game_id']) {
                continue;
            }

            $bonusHuntGame = BonusHuntGame::find($game['id']);

            if (!$bonusHuntGame) {
                continue;
            }
            $bonusHuntGame->game_id = $game['game_id'];
            $bonusHuntGame->name = 'CUSTOM';
            $bonusHuntGame->stake = $game['stake'] ?? 0;
            $bonusHuntGame->result = $game['result'] ?? 0;
            $bonusHuntGame->multiplier = $game['multiplier'] ?? 0;
            $bonusHuntGame->save();

            $totalResult += $game['result'];
        }

        if (isset($request->games[0])) {
            $firstGame = BonusHuntGame::find($request->games[0]['id']);
            if ($firstGame) {
                $bonusHunt = $firstGame->bonusHunt;
                if ($bonusHunt) {
                    $bonusHunt->result = $totalResult;
                    $bonusHunt->save();
                }
            }
        }

        return response()->json([
            'success' => 'Bonus Buy Games updated',
            'total' => $totalResult
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $bonusHuntGame = BonusHuntGame::find($id);
        if(!$bonusHuntGame) {
            return response()->json([
                'message' => 'Bonus Hunt Game not found',
            ]);
        }
        $bonusHuntGame->delete();
        return response()->json([
            'message' => 'Bonus Hunt Game deleted',
        ]);
    }
}
