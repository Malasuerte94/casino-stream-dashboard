<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BonusBuyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $latestStream = $user->streams()->latest()->first();

        $latestBonusBuy = null;
        if ($latestStream) {
            $latestBonusBuy = $latestStream->bonusBuys()->latest()->first();

            if (!$latestBonusBuy) {
                $seeder = Str::random(20);
                $latestBonusBuy = $latestStream->bonusBuys()->create([
                    'name' => 'Bonus Buy',
                    'seed' => $seeder,
                    'user_id' => $user->id,
                    'stream_id' => $latestStream->id
                ]);
            }
        }

        $gamesForBonusBuy = $latestBonusBuy ? $latestBonusBuy->bonusBuyGame : [];
        if (!$gamesForBonusBuy->first()) {
            $gamesForBonusBuy = $latestBonusBuy->bonusBuyGame()->create();
            $gamesForBonusBuy->save();
            $gamesForBonusBuy = [$gamesForBonusBuy];
        }

        return response()->json([
            'bonusBuyGames' => $gamesForBonusBuy,
            'bonusBuy' => $latestBonusBuy,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $bonusBuy = BonusBuy::where('seed', $request->bonusBuy['seed'])->first();
        $bonusBuy->name = $request->bonusBuy['name'];
        $bonusBuy->save();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        $seeder = Str::random(20);
        $latestBonusBuy = BonusBuy::create([
            'name' => 'Bonus Buy',
            'seed' => $seeder,
        ]);


        $gamesForBonusBuy = $latestBonusBuy->bonusBuyGame;
        if(!$gamesForBonusBuy->first()) {
            $gamesForBonusBuy = $latestBonusBuy->bonusBuyGame()->create();
            $gamesForBonusBuy->save();
            $gamesForBonusBuy = [$gamesForBonusBuy];
        }

        return response()->json([
            'bonusBuyGames' => $gamesForBonusBuy,
            'bonusBuy' => $latestBonusBuy,
        ]);
    }

}
