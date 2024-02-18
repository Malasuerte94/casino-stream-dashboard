<?php

namespace App\Http\Controllers;

use App\Models\BonusBuy;
use App\Models\Stream;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class BonusBuyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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

        $gamesForBonusBuy = null;
        if ($latestBonusBuy) {
            $gamesForBonusBuy = $latestBonusBuy->bonusBuyGames;
            if (!$gamesForBonusBuy->first()) {
                $gamesForBonusBuy = $latestBonusBuy->bonusBuyGames()->create();
                $gamesForBonusBuy->save();
                $gamesForBonusBuy = [$gamesForBonusBuy];
            }
        } else {
            $gamesForBonusBuy = [];
        }

        return response()->json([
            'bonusBuyGames' => $gamesForBonusBuy,
            'bonusBuy' => $latestBonusBuy,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request): void
    {
        $bonusBuy = BonusBuy::where('seed', $request->bonusBuy['seed'])->first();
        $bonusBuy->name = $request->bonusBuy['name'];
        $bonusBuy->save();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $user = $request->user();
        $latestStream = $user->streams()->latest()->first();

        $seeder = Str::random(20);

        $latestBonusBuy = BonusBuy::create([
            'name' => 'Bonus Buy',
            'seed' => $seeder,
            'user_id' => $user->id,
            'stream_id' => $latestStream->id
        ]);


        $gamesForBonusBuy = $latestBonusBuy->bonusBuyGames;
        if(!$gamesForBonusBuy->first()) {
            $gamesForBonusBuy = $latestBonusBuy->bonusBuyGames()->create();
            $gamesForBonusBuy->save();
            $gamesForBonusBuy = [$gamesForBonusBuy];
        }

        return response()->json([
            'bonusBuyGames' => $gamesForBonusBuy,
            'bonusBuy' => $latestBonusBuy,
        ]);
    }

}
