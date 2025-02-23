<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuessEntriesResource;
use App\Models\BattlePrediction;
use App\Models\BonusBattle;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\GuessEntry;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BattlePredictionController extends Controller
{
    /**
     * Post or update a user's prediction.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postPrediction(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new Exception('User not found');
            }

            $validatedData = $request->validate([
                'battleId' => 'required|numeric',
                'gameWinner' => 'required|numeric',
                'gameLowestMulti' => 'required|numeric',
                'gameHighestMulti' => 'required|numeric',
            ]);

            $battleId = $validatedData['battleId'];

            // Find the relevant bonus entry
            $bonusBattle =  BonusBattle::find($battleId);

            if (!$bonusBattle) {
                throw new Exception('Bonusul nu a fost găsit');
            }

            if (!$bonusBattle->is_open) {
                throw new Exception('Înscrieri închise');
            }

            // Create or update the guess entry
            $entry = $user->battlePredictions()->updateOrCreate(
                [
                    'bonus_battle_id' => $battleId,
                ],
                [
                    'game_winner_id' => $validatedData['gameWinner'],
                    'game_biggest_multi_id' => $validatedData['gameHighestMulti'],
                    'game_lowest_multi_id' => $validatedData['gameLowestMulti']
                ]
            );

            return response()->json([
                'message' => $entry->wasRecentlyCreated ? "Te-ai înscris cu succes!" : "Predicția a fost actualizată!",
                'entry' => $entry
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retrieve the user's existing prediction for a given bonus.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPrediction(int $id): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new Exception('User not found');
            }

            $prediction = BattlePrediction::where('bonus_battle_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$prediction) {
                return response()->json(['prediction' => null], 200);
            }

            return response()->json([
                'prediction' => [
                    'gameWinner' => $prediction->game_winner_id,
                    'gameHighestMulti' => $prediction->game_biggest_multi_id,
                    'gameLowestMulti' => $prediction->game_lowest_multi_id
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retrieve the user's existing prediction for a given bonus.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPredictions(int $id): JsonResponse
    {
        try {
            $predictions = BattlePrediction::where('bonus_battle_id', $id)
                ->with(['user:id,yt_name,profile_photo_path'])
                ->get();

            if ($predictions->isEmpty()) {
                return response()->json(['predictions' => null], 200);
            }

            return response()->json([
                'predictions' => $predictions
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


}
