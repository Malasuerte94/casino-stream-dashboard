<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\BonusBuyGame;
use Illuminate\Http\Request;

class BonusBuyGameController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //on request we get BonusBuy, check if exist and if exists create new BonusBuyGame
        $bonusBuy = $request->bonusBuyId;
        $bonusBuy = BonusBuy::find($bonusBuy);
        if(!$bonusBuy) {
            return response()->json([
                'message' => 'Bonus Buy not found',
            ]);
        }

        $bonusBuyGame = new BonusBuyGame();
        $bonusBuyGame->bonusBuy()->associate($bonusBuy);
        $bonusBuyGame->save();
        return response()->json([
            'message' => 'Bonus Buy Game created',
            'bonusBuyGame' => $bonusBuyGame,
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
        $totalResult = 0;

        foreach ($request->games as $game) {
            if (!$game['name']) {
                continue;
            }
            $bonusBuyGame = BonusBuyGame::find($game['id']);
            if (!$bonusBuyGame) {
                continue;
            }
            $bonusBuyGame->name = $game['name'];
            $bonusBuyGame->stake = $game['stake'];
            $bonusBuyGame->price = $game['price'];
            $bonusBuyGame->result = $game['result'];
            $bonusBuyGame->multiplier = $game['multiplier'];
            $bonusBuyGame->save();

            $totalResult += $game['result'];
        }


        if (isset($request->games[0])) {
            $firstGame = BonusBuyGame::find($request->games[0]['id']);
            if ($firstGame) {
                $bonusBuy = $firstGame->bonusBuy;
                if ($bonusBuy) {
                    $bonusBuy->result = $totalResult;
                    $bonusBuy->save();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        //destory the row that has the id, the payload contains the id
        $bonusBuyGame = BonusBuyGame::find($id);
        if(!$bonusBuyGame) {
            return response()->json([
                'message' => 'Bonus Buy Game not found',
            ]);
        }
        $bonusBuyGame->delete();
        return response()->json([
            'message' => 'Bonus Buy Game deleted',
        ]);
    }
}
