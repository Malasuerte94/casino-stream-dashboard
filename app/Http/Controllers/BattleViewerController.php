<?php

namespace App\Http\Controllers;

use App\Models\BattleViewer;
use App\Models\BonusBattle;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleViewerController extends Controller
{
    /**
     * Add a bonus battle viewer.
     *
     * @param  string  $userName
     * @param  string  $game
     * @param  int     $creatorId
     * @return string
     */
    public function addBattleViewer($userName, $game, $creatorId): string
    {
        $user = User::find($creatorId);
        if (!$user) {
            return "";
        }

        $cleanGame = preg_replace('/\bnull\b/i', '', $game);
        $cleanGame = trim(preg_replace('/\s+/', ' ', $cleanGame));

        if (empty($cleanGame)) {
            $cleanGame = "N/A";
        }

        $isBonusBattleOpened = $user->userSettings()->where('name', 'battle_selections')->first();

        if($isBonusBattleOpened->value == 0) {
            return "Înscrieri oprite";
        }

        $existingViewer = BattleViewer::where('user_id', $creatorId)
            ->where('user', $userName)
            ->first();

        if ($existingViewer) {
            return "";
        }

        BattleViewer::create([
            'user_id'    => $user->id,
            'user'       => $userName,
            'game'       => $cleanGame,
            'picked'     => false,
            'eliminated' => false,
        ]);

        return '';
    }

    /**
     * Get all battle viewers for the authenticated user.
     *
     * @return JsonResponse
     */
    public function getBattleViewers(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $viewers = $user->battleViewers;

        return response()->json($viewers, 200);
    }

    /**
     * Update a battle viewer.
     *
     * This method accepts updates (via PATCH) to mark a viewer as picked or eliminated.
     * Only the "picked" and "eliminated" fields will be updated.
     */
    public function updateBattleViewer(Request $request, $id): JsonResponse
    {
        $battleViewer = BattleViewer::find($id);
        if (!$battleViewer) {
            return response()->json([
                'error' => 'Battle viewer not found.'
            ], 404);
        }

        // Only update the fields allowed
        $battleViewer->update($request->only(['picked', 'eliminated']));

        return response()->json([
            'message' => 'Battle viewer updated successfully.',
            'data'    => $battleViewer
        ]);
    }

    /**
     * Clear all battle viewers for the authenticated user.
     *
     * This method deletes only the battle viewer records where the user_id matches the authenticated user.
     */
    public function clearBattleViewers(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $viewers = BattleViewer::where('user_id', $user->id)->get();
        foreach ($viewers as $viewer) {
            $viewer->delete();
        }

        return response()->json([
            'message' => 'Your battle viewers have been removed successfully.'
        ]);
    }


    /**
     * Get all battle viewers for the authenticated user.
     *
     * @param $id
     * @return JsonResponse
     */
    public function getBattleViewersPublic($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $viewers = $user->battleViewers;

        return response()->json($viewers, 200);
    }


    /**
     * @param $bonusBattleId
     * @return JsonResponse
     */
    public function getWinners($bonusBattleId): JsonResponse
    {
        $bonusBattle = BonusBattle::find($bonusBattleId);

        if (!$bonusBattle) {
            return response()->json(['error' => 'Battle not found!'], 404);
        }

        if (!$bonusBattle->battlePredictionWinners) {
            return response()->json(['error' => 'Winners not found!'], 404);
        }

        $winnersGame   = $bonusBattle->battlePredictionWinners->gameWinner;
        $winnersLowGame = $bonusBattle->battlePredictionWinners->gameLowest;
        $winnersHighGame = $bonusBattle->battlePredictionWinners->gameHighest;

        $winners = [];

        if ($winnersGame) {
            $gamePrediction = $winnersGame->toArray();
            $gamePrediction['user'] = $winnersGame->user->only(['id', 'name', 'yt_name', 'profile_photo_path']);
            $winners['gameWinner'] = [
                'key' => 'winner',
                'prediction' => $gamePrediction,
                'game'       => $winnersGame->gameWinner->game
            ];
        } else {
            $winners['gameWinner'] = [
                'key' => 'winner',
                'prediction' => null,
                'game'       => null
            ];
        }

        if ($winnersLowGame) {
            $lowestPrediction = $winnersLowGame->toArray();
            $lowestPrediction['user'] = $winnersLowGame->user->only(['id', 'name', 'yt_name', 'profile_photo_path']);
            $winners['gameLowest'] = [
                'key' => 'low',
                'prediction' => $lowestPrediction,
                'game'       => $winnersLowGame->gameLow->game
            ];
        } else {
            $winners['gameLowest'] = [
                'key' => 'low',
                'prediction' => null,
                'game'       => null
            ];
        }

        if ($winnersHighGame) {
            $highestPrediction = $winnersHighGame->toArray();
            $highestPrediction['user'] = $winnersHighGame->user->only(['id', 'name', 'yt_name', 'profile_photo_path']);
            $winners['gameHighest'] = [
                'key' => 'high',
                'prediction' => $highestPrediction,
                'game'       => $winnersHighGame->gameHigh->game
            ];
        } else {
            $winners['gameHighest'] = [
                'key' => 'high',
                'prediction' => null,
                'game'       => null
            ];
        }

        return response()->json(['winners' => $winners], 200);
    }

}
