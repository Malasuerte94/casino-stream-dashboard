<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuessEntriesResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use Exception;
use Illuminate\Http\Request;

class GuessEntriesController extends Controller
{
    /**
     * @param int $bonusListId
     * @param string $type
     * @return bool|string
     */
    public function showEntries(int $bonusListId, string $type): bool|string
    {

        if ($type === 'buy') {
            $bonus = BonusBuy::find($bonusListId);
        } else {
            $bonus = BonusHunt::find($bonusListId);
        }

        $entries = $bonus->guessEntries;
        $entries = GuessEntriesResource::collection($entries);

        return json_encode($entries);

    }

    /**
     * @param Request $request
     * @return string
     */
    public function postEntries(Request $request): string
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new Exception('User not found');
            }

            $request->validate([
                'bonus_type' => 'required|string',
                'bonus_id' => 'required|numeric',
                'estimated_result' => 'required|string|numeric',
            ]);

            $estimated = $request->input('estimated_result');
            $type = $request->input('bonus_type');
            $bonusId = $request->input('bonus_id');

            if($type === 'buy') {
                $bonus = BonusBuy::find($bonusId);
            } else {
                $bonus = BonusHunt::find($bonusId);
            }

            if(!$bonus) {
                throw new Exception('Bonusul nu a fost găsit');
            }
            if(!$bonus->is_open) {
                throw new Exception('Înscrieri închise');
            }

            $entry = $user->guessEntries()->firstOrCreate([
                'bonus_hunt_id' => $type === 'buy' ? null : $bonusId,
                'bonus_buy_id' => $type === 'buy' ? $bonusId : null,
                ],[
                'estimated' => $estimated,
            ]);

            if ($entry->wasRecentlyCreated) {
                return "Te-ai înscris cu success!";
            } else {
                return "Te-ai înscris deja!";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
