<?php

namespace App\Http\Controllers;

use App\Models\BonusHunt;
use App\Models\BonusHuntGame;
use Illuminate\Http\Request;

class BonusHuntGameController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //on request we get BonusHunt, check if exist and if exists create new BonusBuyGame
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {

        foreach ($request->games as $game) {
            if (!$game['name']) {
                continue;
            }
            $bonusHuntGame = BonusHuntGame::find($game['id']);
            if (!$bonusHuntGame) {
                continue;
            }
            $bonusHuntGame->name = $game['name'];
            $bonusHuntGame->stake = $game['stake'];
            $bonusHuntGame->result = $game['result'];
            $bonusHuntGame->multiplier = $game['multiplier'];
            $bonusHuntGame->save();
        }

        return response()->json([
            'success' => 'Bonus Buy Games updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
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
