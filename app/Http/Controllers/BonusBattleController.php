<?php

namespace App\Http\Controllers;

use App\Models\BonusBattle;
use App\Models\BonusStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BonusBattleController extends Controller
{

    public function getActiveBattle(): JsonResponse
    {
        $activeBattle = BonusBattle::with('concurrents')->latest()->first();
        return response()->json(['battle' => $activeBattle]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'stake' => 'required|numeric|min:0',
            'concurrents' => 'required|array|min:2',
            'concurrents.*.name' => 'required|string|max:255',
        ]);

        $bonusBattle = BonusBattle::create([
            'title' => $validated['title'],
            'stake' => $validated['stake'],
        ]);

        foreach ($validated['concurrents'] as $concurrent) {
            $bonusBattle->concurrents()->create(['name' => $concurrent['name']]);
        }

        return response()->json(['message' => 'Bonus battle created successfully!', 'bonus_battle' => $bonusBattle], 201);
    }

    // Add a stage to a bonus battle
    public function addStage(BonusBattle $bonusBattle): JsonResponse
    {
        $stageName = 'Stage ' . ($bonusBattle->stages()->count() + 1);

        $stage = $bonusBattle->stages()->create(['name' => $stageName]);

        return response()->json(['message' => 'Stage added successfully!', 'stage' => $stage], 201);
    }

    // Add scores for a stage
    public function addScores(Request $request, BonusBattle $bonusBattle): JsonResponse
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:bonus_stages,id',
            'scores' => 'required|array',
            'scores.*.concurrent_id' => 'required|exists:bonus_concurrents,id',
            'scores.*.score' => 'required|integer|min:0',
            'scores.*.winner' => 'required|boolean',
        ]);

        $stage = BonusStage::findOrFail($validated['stage_id']);

        foreach ($validated['scores'] as $score) {
            $stage->scores()->create([
                'bonus_concurrent_id' => $score['concurrent_id'],
                'score' => $score['score'],
                'winner' => $score['winner'],
            ]);
        }

        return response()->json(['message' => 'Scores added successfully!'], 200);
    }

    // Get all stages for a bonus battle
    public function getStages(BonusBattle $bonusBattle): JsonResponse
    {
        $stages = $bonusBattle->stages()->with(['scores.concurrent'])->get();

        return response()->json(['stages' => $stages], 200);
    }
}
