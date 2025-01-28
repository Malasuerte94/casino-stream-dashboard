<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\BonusBuyGame;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BonusBuyGameController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $totalResult = 0;
        $priceList = 0;

        foreach ($request->games as $game) {
            if (!$game['game_id']) {
                continue;
            }
            $bonusBuyGame = BonusBuyGame::find($game['id']);
            if (!$bonusBuyGame) {
                continue;
            }
            $bonusBuyGame->game_id = $game['game_id'];
            $bonusBuyGame->name = 'CUSTOM';
            $bonusBuyGame->stake = $game['stake'] ?? 0;
            $bonusBuyGame->price = $game['price'] ?? 0;
            $bonusBuyGame->result = $game['result'] ?? 0;
            $bonusBuyGame->multiplier = $game['multiplier'] ?? 0;
            $bonusBuyGame->save();

            $priceList += $game['price'];
            $totalResult += $game['result'];
        }


        if (isset($request->games[0])) {
            $firstGame = BonusBuyGame::find($request->games[0]['id']);
            if ($firstGame) {
                $bonusBuy = $firstGame->bonusBuy;
                if ($bonusBuy) {
                    $bonusBuy->start = $priceList;
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
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
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
