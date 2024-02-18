<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\Stream;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            $gamesForBonus = $latestBonus->bonusBuyGames;
        } else {
            $latestBonus = $latestStream
                ->bonusHunts()
                ->latest()
                ->first();
            $gamesForBonus = $latestBonus->bonusHuntGames;
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
     * @return void
     */
    public function closeBonusList(Request $request): void {
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

        $latestBonus->ended = true;
        $latestBonus->save();
    }

    public function getBonusWinner(int $streamerId) {
        $streamer = User::find($streamerId);
        $stream = $streamer->streams()->latest()->first();
        $settings = $streamer->userSettings->where('name', 'bonus_list')->first();
        if ($settings->value === 'buy') {
            $bonus = $stream->bonusBuys()->latest()->first();
        } else {
            $bonus = $stream->bonusHunts()->latest()->first();
        }

        if($bonus->ended) {

            if ($settings->value === 'buy') {
                $existentWinners = Winner::where('bonus_buy_id', $bonus->id)->first();
            } else {
                $existentWinners = Winner::where('bonus_hunt_id', $bonus->id)->first();
            }

            if($existentWinners) {
                return response()->json(['status' => 'pending'], 204);
            }


            $result = $bonus->result;

            if($settings->value === 'buy') {
                $biggestMultiplierGame = $bonus->bonusBuyGames()->orderBy('multiplier')->first();
                $lowestMultiplierGame = $bonus->bonusBuyGames()->orderByDesc('multiplier')->first();
            } else {
                $biggestMultiplierGame = $bonus->bonusHuntGames()->orderBy('multiplier')->first();
                $lowestMultiplierGame = $bonus->bonusHuntGames()->orderByDesc('multiplier')->first();
            }

            $biggestMultiplier = $biggestMultiplierGame->multiplier;
            $lowestMultiplier = $lowestMultiplierGame->multiplier;
            $gameWinnerId = $biggestMultiplierGame->id;
            $gameWinnerName = $biggestMultiplierGame->name;

            $bonusEntries = $bonus->guessEntries()->get();

            $resultWinner = $bonusEntries->sortBy(function ($entry) {
                return abs($entry->estimated - $entry->result);
            })->first();
            $resultWinnerEstimated = $resultWinner->estimated;
            $resultWinnerUser = $resultWinner->user;


            $biggestMultiplierWinner = $bonusEntries->sortBy(function ($entry) use ($biggestMultiplier) {
                return abs($entry->biggest_multi - $biggestMultiplier);
            })->first();
            $biggestMultiplierWinnerMultiplier = $biggestMultiplierWinner->biggest_multi;
            $biggestMultiplierWinnerUser = $biggestMultiplierWinner->user;

            $lowestMultiplierWinner = $bonusEntries->sortBy(function ($entry) use ($lowestMultiplier) {
                return abs($entry->lowest_multi - $lowestMultiplier);
            })->first();
            $lowestMultiplierWinnerMultiplier = $lowestMultiplierWinner->lowest_multi;
            $lowestMultiplierWinnerUser = $lowestMultiplierWinner->user;

            $gameWinnerWinner = $bonusEntries->where('game_winner', $gameWinnerId)->first();

            if ($gameWinnerWinner === null) {
                $gameWinnerWinnerUser = $resultWinnerUser;
            } else {
                $gameWinnerWinnerUser = $gameWinnerWinner->user;
            }

            Winner::create([
                'user_id' => $resultWinnerUser->id,
                'bonus_hunt_id' => $settings->value === 'buy' ? null : $bonus->id,
                'bonus_buy_id' => $settings->value === 'buy' ? $bonus->id : null,
                'win_result' => true
            ]);

            Winner::create([
                'user_id' => $biggestMultiplierWinnerUser->id,
                'bonus_hunt_id' => $settings->value === 'buy' ? null : $bonus->id,
                'bonus_buy_id' => $settings->value === 'buy' ? $bonus->id : null,
                'win_big_m' => true
            ]);

            Winner::create([
                'user_id' => $lowestMultiplierWinnerUser->id,
                'bonus_hunt_id' => $settings->value === 'buy' ? null : $bonus->id,
                'bonus_buy_id' => $settings->value === 'buy' ? $bonus->id : null,
                'win_small_m' => true
            ]);

            Winner::create([
                'user_id' => $gameWinnerWinnerUser->id,
                'bonus_hunt_id' => $settings->value === 'buy' ? null : $bonus->id,
                'bonus_buy_id' => $settings->value === 'buy' ? $bonus->id : null,
                'win_game' => true
            ]);

            return response()->json([
                'list_results' => [
                    'result' => $result,
                    'biggestMultiplier' => $biggestMultiplier,
                    'lowestMultiplier' => $lowestMultiplier,
                    'gameWinner' => $gameWinnerName,
                ],
                'winners' => [
                    'resultWinner' => [
                        'user' => $resultWinnerUser,
                        'pick' => $resultWinnerEstimated,
                    ],
                    'biggestMultiplierWinner' => [
                        'user' => $biggestMultiplierWinnerUser,
                        'pick' => $biggestMultiplierWinnerMultiplier,
                    ],
                    'lowestMultiplierWinner' => [
                        'user' => $lowestMultiplierWinnerUser,
                        'pick' => $lowestMultiplierWinnerMultiplier
                    ],
                    'gameWinnerWinner' => [
                        'user' => $gameWinnerWinnerUser,
                        'pick' => $gameWinnerName,
                    ],
                ]
            ]);
        } else {
            return response()->json(['status' => 'pending'], 204);
        }
    }

}
