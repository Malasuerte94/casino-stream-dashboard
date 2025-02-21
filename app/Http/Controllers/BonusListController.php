<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use App\Models\Winner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BonusListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function index(int $id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $bonusListSetting = $user->userSettings->where('name', 'bonus_list')->first()->value ?? 'hunt';

        $latestStream = $user
            ->streams()
            ->latest()
            ->first();

        if ($bonusListSetting === 'buy') {
            $latestBonus = $latestStream
                ->bonusBuys()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusBuyGames->load('game');
        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusHuntGames->load('game');
        }

        return response()->json([
            'list-id' => $latestBonus->id,
            'bonusListGames' => $gamesForBonus,
            'bonusList' => $latestBonus,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUrl(Request $request): JsonResponse
    {
        $user = $request->user();

        $bonusListSetting = $user->userSettings->where('name', 'bonus_list')->first()->value ?? 'hunt';

        $latestStream = $user
            ->streams()
            ->latest()
            ->first();

        if ($bonusListSetting === 'buy') {
            $latestBonus = $latestStream
                ->bonusBuys()
                ->latest()
                ->first();

        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
        }

        return response()->json([
            'list-id' => $latestBonus->id,
            'is_open' => $latestBonus->is_open,
            'bonusList' => $latestBonus,
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function closeBonusList(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'close' => 'required',
                'list_id' => 'required',
                'type' => 'required|string'
            ]);

            $listId = $request->input('list_id');
            $type = $request->input('type');

            if ($type === 'buy') {
                $latestBonus = BonusBuy::find($listId);
            } else {
                $latestBonus = BonusHunt::find($listId);
            }

            if($latestBonus->ended === 0) {
                $latestBonus->ended = true;
                $latestBonus->is_open = false;
                $latestBonus->save();

                $this->generateWinners($latestBonus);

                $discord = new DiscordController();
                $discord->sendHuntBuyMessage($latestBonus);
            } else {
                return response()->json(['status' => 'Deja Închis'], 200);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @throws Exception
     */
    public function generateWinners($latestBonus): void
    {
        if (!$latestBonus->ended) {
            throw new Exception("Bonusul nu a fost încă închis!");
        }

        // Check if winners were already generated
        $existingWinner = Winner::where('bonus_hunt_id', $latestBonus->id)
            ->orWhere('bonus_buy_id', $latestBonus->id)
            ->exists();

        if ($existingWinner) {
            return;
        }

        // Determine if it's a bonus hunt or buy
        $isBuy = $latestBonus instanceof BonusBuy;
        $bonusType = $isBuy ? 'buy' : 'hunt';

        // Fetch games and determine multipliers
        $games = $isBuy ? $latestBonus->bonusBuyGames() : $latestBonus->bonusHuntGames();
        $biggestMultiplierGame = $games->orderByDesc('multiplier')->first();
        $lowestMultiplierGame = $games->orderBy('multiplier')->first();

        $biggestMultiplier = $biggestMultiplierGame->multiplier ?? 0;
        $lowestMultiplier = $lowestMultiplierGame->multiplier ?? 0;
        $gameWinnerId = $biggestMultiplierGame->id ?? null;
        $gameWinnerName = $biggestMultiplierGame->name ?? null;

        // Get all predictions
        $bonusEntries = $latestBonus->guessEntries()->get();
        if ($bonusEntries->isEmpty()) {
            return;
        }
        // Find the closest predicted result
        $resultWinner = $bonusEntries->sortBy(fn ($entry) => abs($entry->estimated - $latestBonus->result))->first();

        // Find the closest predicted highest multiplier
        $biggestMultiplierWinner = $bonusEntries->sortBy(fn ($entry) => abs($entry->biggest_multi - $biggestMultiplier))->first();

        // Find the closest predicted lowest multiplier
        $lowestMultiplierWinner = $bonusEntries->sortBy(fn ($entry) => abs($entry->lowest_multi - $lowestMultiplier))->first();

        // Find the user who picked the correct winning game
        $gameWinnerWinner = $bonusEntries->where('game_winner', $gameWinnerId)->first();

        // Store winners in the database (only one row per hunt/buy)
        Winner::create([
            'bonus_hunt_id' => $isBuy ? null : $latestBonus->id,
            'bonus_buy_id' => $isBuy ? $latestBonus->id : null,
            'win_closest_result' => $resultWinner?->id,
            'win_closest_biggest_multi' => $biggestMultiplierWinner?->id,
            'win_closest_lowest_multi' => $lowestMultiplierWinner?->id,
            'win_exact_biggest_multi_game' => $gameWinnerWinner?->id,
            'win_exact_lowest_multi_game' => $lowestMultiplierWinner?->id,
        ]);

    }

    public function getBonusWinner(int $bonuHuntId): JsonResponse
    {

        // Get latest ended bonus
        $bonus = BonusHunt::findOrFail($bonuHuntId);
        if (!$bonus) {
            return response()->json(['error' => 'No ended bonus found'], 404);
        }

        // Fetch the winners entry
        $winner = Winner::where('bonus_hunt_id', $bonus->id)
            ->orWhere('bonus_buy_id', $bonus->id)
            ->first();

        if (!$winner) {
            return response()->json(['message' => 'No winners found'], 204);
        }

        return response()->json([
            'bonus_id' => $bonus->id,
            'bonus_type' => 'hunt',
            'results' => [
                'result' => $bonus->result,
                'biggestMultiplier' => $bonus->bonusHuntGames()->max('multiplier'),
                'lowestMultiplier' => $bonus->bonusHuntGames()->min('multiplier'),
            ],
            'winners' => [
                'resultWinner' => $winner->win_closest_result ? $winner->closestResult->user : null,
                'biggestMultiplierWinner' => $winner->win_closest_biggest_multi ? $winner->closestBiggestMulti->user : null,
                'lowestMultiplierWinner' => $winner->win_closest_lowest_multi ? $winner->closestLowestMulti->user : null,
                'exactBiggestMultiplierGame' => $winner->win_exact_biggest_multi_game ? $winner->exactBiggestMultiGame->user : null,
                'exactLowestMultiplierGame' => $winner->win_exact_lowest_multi_game ? $winner->exactLowestMultiGame->user : null,
            ]
        ], 200);
    }


}
