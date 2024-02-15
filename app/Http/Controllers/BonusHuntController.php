<?php

namespace App\Http\Controllers;

use App\Models\BonusHunt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BonusHuntController extends Controller
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

        $latestBonusHunt = null;
        if ($latestStream) {
            $latestBonusHunt = $latestStream->bonusHunts()->latest()->first();

            if (!$latestBonusHunt) {
                $seeder = Str::random(20);
                $latestBonusHunt = $latestStream->bonusHunts()->create([
                    'name' => 'Bonus Hunt',
                    'seed' => $seeder,
                    'user_id' => $user->id,
                    'stream_id' => $latestStream->id
                ]);
            }
        }

        $gamesForBonusHunt = $latestBonusHunt ? $latestBonusHunt->bonusHuntGames : [];
        if (!$gamesForBonusHunt->first()) {
            $gamesForBonusHunt = $latestBonusHunt->bonusHuntGame()->create();
            $gamesForBonusHunt->save();
            $gamesForBonusHunt = [$gamesForBonusHunt];
        }

        return response()->json([
            'bonusHuntGames' => $gamesForBonusHunt,
            'bonusHunt' => $latestBonusHunt,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $latestStream = $user->streams()->latest()->first();

        $seeder = Str::random(20);
        $latestBonusHunt = BonusHunt::create([
            'name' => 'Bonus Hunt',
            'seed' => $seeder,
            'user_id' => $user->id,
            'stream_id' => $latestStream->id
        ]);


        $gamesForBonusHunt = $latestBonusHunt->bonusHuntGame;
        if(!$gamesForBonusHunt->first()) {
            $gamesForBonusHunt = $latestBonusHunt->bonusHuntGame()->create();
            $gamesForBonusHunt->save();
            $gamesForBonusHunt = [$gamesForBonusHunt];
        }

        return response()->json([
            'bonusHuntGames' => $gamesForBonusHunt,
            'bonusHunt' => $latestBonusHunt,
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
        $bonusHunt = BonusHunt::where('seed', $request->bonusHunt['seed'])->first();
        $bonusHunt->name = $request->bonusHunt['name'];
        $bonusHunt->start = $request->bonusHunt['start'];
        $bonusHunt->save();
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $hunts = $user->streams()->with('bonusHunts')->get()->pluck('bonusHunts')->collapse();

        return response()->json([
            'hunts' => $hunts,
        ]);
    }

}
