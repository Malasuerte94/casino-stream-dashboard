<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuessEntriesResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\GuessEntry;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuessEntriesController extends Controller
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

            // Validate request data
            $validatedData = $request->validate([
                'bonusId' => 'required|numeric',
                'totalPayout' => 'required|numeric',
                'highestMulti' => 'required|numeric',
                'lowestMulti' => 'required|numeric',
                'bestGame' => 'required|numeric',
                'worstGame' => 'required|numeric',
                'listType' => 'required|string|in:buy,hunt',
            ]);

            $bonusId = $validatedData['bonusId'];
            $type = $validatedData['listType'];

            // Find the relevant bonus entry
            $bonus = ($type === 'buy') ? BonusBuy::find($bonusId) : BonusHunt::find($bonusId);

            if (!$bonus) {
                throw new Exception('Bonusul nu a fost găsit');
            }

            if (!$bonus->is_open) {
                throw new Exception('Înscrieri închise');
            }

            // Create or update the guess entry
            $entry = $user->guessEntries()->updateOrCreate(
                [
                    'bonus_hunt_id' => $type === 'hunt' ? $bonusId : null,
                    'bonus_buy_id' => $type === 'buy' ? $bonusId : null,
                ],
                [
                    'estimated' => $validatedData['totalPayout'],
                    'game_highest_id' => $validatedData['bestGame'],
                    'game_lowest_id' => $validatedData['worstGame'],
                    'biggest_multi' => $validatedData['highestMulti'],
                    'lowest_multi' => $validatedData['lowestMulti'],
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

            // Find existing prediction
            $prediction = GuessEntry::where('bonus_hunt_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$prediction) {
                return response()->json(['prediction' => null], 200);
            }

            return response()->json([
                'prediction' => [
                    'totalPayout' => $prediction->estimated,
                    'highestMulti' => $prediction->biggest_multi,
                    'lowestMulti' => $prediction->lowest_multi,
                    'bestGame' => $prediction->game_highest_id,
                    'worstGame' => $prediction->game_lowest_id,
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
            $predictions = GuessEntry::where('bonus_hunt_id', $id)
                ->with(['user:id,yt_name,profile_photo_path']) // Eager load user with selected fields
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


    /**
     * Retrieve the user's existing prediction for a bonus buy
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getBblPredictions(int $id): JsonResponse
    {
        try {
            $predictions = GuessEntry::where('bonus_buy_id', $id)
                ->with(['user:id,yt_name,profile_photo_path']) // Eager load user with selected fields
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
