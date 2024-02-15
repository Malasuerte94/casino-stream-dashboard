<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuessEntriesResource;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use Exception;
use Illuminate\Http\Request;

class GuessEntriesController extends Controller
{
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

    public function postEntries(Request $request) {
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
                throw new Exception('Bonus not found');
            }

            $user->guessEntries()->firstOrCreate([
                'bonus_hunt_id' => $type === 'buy' ? null : $bonusId,
                'bonus_buy_id' => $type === 'buy' ? $bonusId : null,
                ],[
                'estimated' => $estimated,
            ]);

            return 'Confirmed';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
