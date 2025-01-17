<?php

namespace App\Http\Controllers;

use App\Models\BonusBattle;
use App\Models\BonusStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BonusBattleController extends Controller
{

    public function getActiveBattle(): JsonResponse
    {
        $activeBattle = BonusBattle::where('active', true)
            ->first();

        if (!$activeBattle) {
            return response()->json(['battle' => null, 'stage' => null, 'concurrents' => []]);
        }
        $activeStage = $this->getLastActiveStage($activeBattle) ?? $this->getLastEndedStage($activeBattle);

        $remainingConcurrentIds = $this->getRemainingConcurrentsInStage($activeBattle);

        $remainingConcurrents = $activeBattle->concurrents()
            ->whereIn('id', $remainingConcurrentIds)
            ->take(2)
            ->get();

        $winner = $this->getFinalWinner($activeBattle, $remainingConcurrentIds);

        return response()->json([
            'battle' => $activeBattle,
            'stage' => $activeStage,
            'concurrents' => $remainingConcurrents,
            'winner' => $winner,
        ]);
    }


    private function getFinalWinner($activeBattle, $remainingConcurrents)
    {
        // Get winners from the last ended stage
        $winnersFromLastStage = $this->getWinnersFromLastEndedStage($activeBattle);

        // Check if there is only one winner left and no remaining concurrents in the current stage
        if (count($winnersFromLastStage) === 1 && (empty($remainingConcurrents) || count($remainingConcurrents) === 1)) {
            return $activeBattle->concurrents()->find($winnersFromLastStage[0]);
        }

        return null;
    }


    //helpers
    private function getLastActiveStage($activeBattle)
    {
        return $activeBattle->stages()
            ->where('active', true)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    private function getLastEndedStage($activeBattle)
    {
        return $activeBattle->stages()
            ->where('active', false)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    private function getWinnersFromLastEndedStage($activeBattle)
    {
        // Get the last ended stage
        $lastEndedStage = $this->getLastEndedStage($activeBattle);

        if (!$lastEndedStage) {
            return $activeBattle->concurrents()->pluck('id')->toArray(); // Return an empty array if no ended stage exists
        }

        // Get the IDs of the winners from the stage_scores table
        return DB::table('stage_scores')
            ->where('bonus_stage_id', $lastEndedStage->id)
            ->where('winner', true)
            ->pluck('bonus_concurrent_id')
            ->toArray();
    }

    private function getWinnersFromActiveStage($activeBattle)
    {
        // Get the current active stage
        $activeStage = $this->getLastActiveStage($activeBattle);

        if (!$activeStage) {
            return []; // Return an empty array if no active stage exists
        }

        // Get the IDs of the winners from the stage_scores table
        return DB::table('stage_scores')
            ->where('bonus_stage_id', $activeStage->id)
            ->where('winner', true)
            ->pluck('bonus_concurrent_id')
            ->toArray();
    }

    private function getScoredConcurrentsInCurrentStage($activeBattle)
    {
        // Get the current active stage
        $activeStage = $this->getLastActiveStage($activeBattle);

        if (!$activeStage) {
            return []; // Return an empty array if no active stage exists
        }

        // Retrieve IDs of scored concurrents in the current stage
        return DB::table('stage_scores')
            ->where('bonus_stage_id', $activeStage->id)
            ->pluck('bonus_concurrent_id')
            ->toArray();
    }


    private function getRemainingConcurrentsInStage($activeBattle): array
    {
        $winnersFromLastStage = $this->getWinnersFromLastEndedStage($activeBattle);
        $winnersFromActiveStage = $this->getScoredConcurrentsInCurrentStage($activeBattle);

        return array_diff($winnersFromLastStage, $winnersFromActiveStage);
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
            'active' => true,
        ]);

        foreach ($validated['concurrents'] as $concurrent) {
            $bonusBattle->concurrents()->create(['name' => $concurrent['name']]);
        }

        $bonusBattle->stages()->create(['name' => 'Stage 1']);

        return response()->json(['message' => 'Bonus battle created successfully!', 'bonus_battle' => $bonusBattle], 201);
    }

    public function finishRound(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'active_stage' => 'required|exists:bonus_stages,id',
            'active_battle' => 'required|exists:bonus_battles,id',
            'scores' => 'required|array|min:2',
            'scores.*.concurrent_id' => 'required|exists:bonus_concurrents,id',
            'scores.*.total_score' => 'required|numeric|min:0',
        ]);

        $stage = BonusStage::findOrFail($validated['active_stage']);
        $battle = BonusBattle::findOrFail($validated['active_battle']);

        $sortedScores = collect($validated['scores'])->sortByDesc('total_score');
        $winner = $sortedScores->first();
        $loser = $sortedScores->last();

        $stage->scores()->createMany([
            [
                'bonus_concurrent_id' => $winner['concurrent_id'],
                'score' => $winner['total_score'],
                'winner' => true,
            ],
            [
                'bonus_concurrent_id' => $loser['concurrent_id'],
                'score' => $loser['total_score'],
                'winner' => false,
            ],
        ]);

        $remainingConcurrents = $this->getRemainingConcurrentsInStage($battle);

        if (count($remainingConcurrents) < 2) {
            $stage->update(['active' => false]);
            $winners = $stage->scores()->where('winner', true)->pluck('bonus_concurrent_id')->toArray();
            logger()->info('Winners: ' . implode(', ', $winners));
            if (count($winners) >= 2) {
                $stageName = 'Stage ' . ($battle->stages()->count() + 1);
                $newStage = $battle->stages()->create(['name' => $stageName, 'active' => true]);
                return response()->json([
                    'message' => 'New stage created!',
                    'new_stage' => $newStage,
                ]);
            }

            // If no new stage, the battle is complete
            return response()->json([
                'message' => 'Battle completed!',
                'new_stage' => null,
            ]);
        }

        return response()->json([
            'message' => 'Next round ready!',
        ]);
    }

    // Get all stages for a bonus battle
    public function endBattle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bonus_battle_id' => 'required|exists:bonus_battles,id',
        ]);

        $bonusBattle = BonusBattle::findOrFail($validated['bonus_battle_id']);

        $bonusBattle->stages()->update(['active' => 0]);
        $bonusBattle->update(['active' => 0]);

        $stages = $bonusBattle->stages()->with(['scores.concurrent'])->get();

        return response()->json(['stages' => $stages], 200);
    }

}
