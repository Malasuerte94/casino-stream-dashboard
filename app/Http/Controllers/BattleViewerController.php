<?php

namespace App\Http\Controllers;

use App\Models\BattleViewer;
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
        // Get the latest bonus battle created by the specified creator
        $user = User::find($creatorId);

        if (!$user) {
            return "Nu știm pe cine cauți...";
        }

        $existingViewer = BattleViewer::where('user_id', $creatorId)
            ->where('user', $userName)
            ->first();

        if ($existingViewer) {
            return '@'.$userName.' deja inscris!';
        }

        BattleViewer::create([
            'user_id' => $user->id,
            'user'            => $userName,
            'game'            => $game,
            'picked'          => false,
            'eliminated'      => false,
        ]);

        return 'Inscris!';
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

        // Delete only the records for the authenticated user.
        BattleViewer::where('user_id', $user->id)->delete();

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
}
