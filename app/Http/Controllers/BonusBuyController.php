<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BonusBuyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $latestBonusBuy = BonusBuy::latest()->first();
        if(!$latestBonusBuy) {
            $seeder = Str::random(20);
            $latestBonusBuy = BonusBuy::create([
                'name' => 'Bonus Buy',
                'seed' => $seeder,
            ]);
        }

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


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BonusBuy  $bonusBuy
     * @return \Illuminate\Http\Response
     */
    public function destroy(BonusBuy $bonusBuy)
    {
        //
    }
}
