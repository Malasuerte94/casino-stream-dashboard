<?php

namespace App\Http\Controllers;

use App\Models\BonusBattle;
use App\Models\BonusConcurrent;
use App\Models\Bracket;
use App\Models\StageScore;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BonusBattleController extends Controller
{

    /**
     * Get streamer BATTLE INFO
     * @param Request $request
     * @return JsonResponse
     */
    public function getActiveBattle(Request $request): JsonResponse
    {
        $user = $request->user();

        // Fetch the active battle for the authenticated user
        $activeBattle = BonusBattle::where('active', true)
            ->where('user_id', $user->id)
            ->first();

        // Count the total bonus battles for the user
        $totalBattlesCount = BonusBattle::where('user_id', $user->id)->count();

        if (!$activeBattle) {
            return response()->json([
                'battle' => null,
                'stage' => null,
                'concurrents' => [],
                'total_battles' => $totalBattlesCount,
            ]);
        }

        $infoBattle = $this->getCurentBattleDetails($activeBattle);
        $infoBattle['total_battles'] = $totalBattlesCount;

        return response()->json($infoBattle);
    }


    /**
     * Get PUBLIC BATTLE INFO
     * @param $id
     * @return JsonResponse
     */
    public function getBonusBattleInfo($id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);
        $activeBattle = $user->bonusBattles()->where('active', true)->first();

        if (!$activeBattle) {
            return response()->json(['battle' => null, 'stage' => null, 'concurrents' => []]);
        }

        $activeInfo = $this->getCurentBattleDetails($activeBattle);
        $activeStage = $activeBattle->lastActiveStage ?? $activeBattle->lastEndedStage;

        $activeInfo['all_concurrents'] = $activeBattle->concurrents()->get();
        $activeInfo['current_score'] = $activeStage->scores()->get();

        return response()->json($activeInfo);
    }


    private function getCurentBattleDetails($activeBattle): array
    {
        $activeStage = $activeBattle->lastActiveStage ?? $activeBattle->lastEndedStage;

        $activeStageScores = $activeStage->activeBracket->scores()->get();
        $nextConcurrents = $this->getNextConcurrents($activeBattle);

        return [
            'battle' => $activeBattle,
            'stage' => $activeStage,
            'bracket' => $activeStage->activeBracket,
            'concurrents' => $nextConcurrents,
            'scores' => $activeStageScores,
            'winner' => null,
        ];
    }

    private function getNextConcurrents($activeBattle): array
    {
        $activeStage = $activeBattle->lastActiveStage;

        if (!$activeStage) {
            return []; // No active stage, no concurrents to return
        }

        $nextBracket = $activeStage->activeBracket;
        if (!$nextBracket) {
            return []; // No unfinished brackets, no concurrents to return
        }

        $participantA = $nextBracket->participantA;
        $participantB = $nextBracket->participantB;

        return array_filter([$participantA, $participantB]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'stake' => 'string|nullable',
            'concurrents' => 'required|array|min:2',
            'concurrents.*.name' => 'required|string|max:255',
            'concurrents.*.for_user' => 'string|max:255|nullable',
        ]);

        $bonusBattle = BonusBattle::create([
            'title' => $validated['title'],
            'stake' => $validated['stake'] ?? 'Aleatorie',
            'active' => true,
            'user_id' => $user->id,
        ]);

        foreach ($validated['concurrents'] as $concurrent) {
            $bonusBattle->concurrents()->create([
                'name' => $concurrent['name'],
                'for_user' => $concurrent['for_user']
            ]);
        }

        $bonusBattle->stages()->create(['name' => 'Stage 1']);

        $this->generateFirstBrackets($bonusBattle);

        return response()->json(['message' => 'Bonus battle created successfully!', 'bonus_battle' => $bonusBattle],
            201);
    }


    /**
     * Generate brackets for a given BonusBattle.
     *
     * @param BonusBattle $bonusBattle
     * @return void
     */
    function generateFirstBrackets(BonusBattle $bonusBattle): void
    {
        $concurrents = $bonusBattle->concurrents;

        $activeStage = $bonusBattle->lastActiveStage;

        if (!$activeStage) {
            $activeStage = $bonusBattle->stages()->create(['name' => 'Stage 1']);
        }
        $shuffledConcurrents = $concurrents->shuffle();

        $pairs = $shuffledConcurrents->chunk(2)->map(function ($chunk) {
            return $chunk->values()->all();
        })->toArray();

        foreach ($pairs as $pair) {
            if (empty($pair[0])) {
                continue;
            }
            Bracket::create([
                'bonus_stage_id' => $activeStage->id,
                'participant_a_id' => $pair[0]->id,
                'participant_b_id' => $pair[1]->id ?? null,
                'is_finished' => false,
            ]);
        }
    }


    /**
     * Generate a new stage and brackets for a BonusBattle.
     *
     * @param BonusBattle $bonusBattle
     * @return void
     */
    public function generateNewStageAndBrackets(BonusBattle $bonusBattle): void
    {
        // Fetch the last ended stage
        $lastEndedStage = $bonusBattle->lastEndedStage;

        if (!$lastEndedStage) {
            return;
        }

        // Get winners from the brackets of the last stage
        $winnerIds = $lastEndedStage->brackets()
            ->whereNotNull('winner_id') // Ensure the bracket has a winner
            ->pluck('winner_id')
            ->toArray();

        $remainingParticipants = count($winnerIds);

        if ($remainingParticipants < 2) {
            return;
        }

        // Determine stage name based on remaining participants
        $stageName = match ($remainingParticipants) {
            2 => 'Final',
            4 => 'Semifinal',
            default => 'Stage ' . ($bonusBattle->stages()->count() + 1),
        };

        // Create the new stage
        $newStage = $bonusBattle->stages()->create([
            'name' => $stageName,
            'active' => true,
        ]);

        // Create brackets for the new stage
        $winners = BonusConcurrent::whereIn('id', $winnerIds)->get();
        $pairs = $winners->shuffle()->chunk(2);

        foreach ($pairs as $pair) {
            Bracket::create([
                'bonus_stage_id' => $newStage->id,
                'participant_a_id' => $pair[0]->id,
                'participant_b_id' => $pair[1]->id ?? null, // Handle odd participants
                'is_finished' => false,
            ]);
        }

    }

    private function endBracket(Bracket $bracket): void
    {
        // Fetch scores for both participants
        $participantA = $bracket->participantA;
        $participantB = $bracket->participantB;

        if (!$participantA || !$participantB) {
            return;
        }

        // Calculate totals for Participant A
        $scoresA = $participantA->scores()->where('bracket_id', $bracket->id)->get();
        $totalScoreA = $scoresA->sum('score');

        // Calculate totals for Participant B
        $scoresB = $participantB->scores()->where('bracket_id', $bracket->id)->get();
        $totalScoreB = $scoresB->sum('score');

        // Determine the winner
        $winnerId = $totalScoreA > $totalScoreB ? $participantA->id : $participantB->id;

        // Update the bracket with winner details
        $bracket->update([
            'is_finished' => true,
            'winner_id' => $winnerId,
        ]);
    }

    public function finishRound(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'active_bracket' => 'required|exists:brackets,id',
        ]);

        $bracket = Bracket::findOrFail($validated['active_bracket']);

        $this->endBracket($bracket);

        $nextConcurrents = $this->getNextConcurrents($bracket->stage->bonusBattle);
        if (empty($nextConcurrents)) {
            $bracket->stage->update([
                'active' => false
            ]);
            $this->generateNewStageAndBrackets($bracket->stage->bonusBattle);
        }

        return response()->json([
            'message' => 'Round finished successfully!'
        ]);
    }

    public function addScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'active_bracket' => 'required|exists:brackets,id',
            'bracket' => 'required|array|min:2',
            'bracket.*.id' => 'required|exists:bonus_concurrents,id',
            'bracket.*.scores' => 'required|array|min:1',
            'bracket.*.scores.*.id' => 'nullable|numeric',
            'bracket.*.scores.*.bracket_id' => 'required|exists:brackets,id',
            'bracket.*.scores.*.bonus_concurrent_id' => 'required|exists:bonus_concurrents,id',
            'bracket.*.scores.*.cost_buy' => 'required|numeric|min:0',
            'bracket.*.scores.*.result_buy' => 'required|numeric|min:0',
            'bracket.*.scores.*.score' => 'required|numeric|min:0',
        ]);

        $bracket = Bracket::findOrFail($validated['active_bracket']);

        foreach ($validated['bracket'] as $row) {
            foreach ($row['scores'] as $score) {
                if (!empty($score['id'])) {
                    $bracket->scores()->where('id', $score['id'])->update([
                        'bonus_concurrent_id' => $score['bonus_concurrent_id'],
                        'score' => $score['score'],
                        'cost_buy' => $score['cost_buy'],
                        'result_buy' => $score['result_buy'],
                    ]);
                } else {
                    $bracket->scores()->create([
                        'bonus_concurrent_id' => $score['bonus_concurrent_id'],
                        'score' => $score['score'],
                        'cost_buy' => $score['cost_buy'],
                        'result_buy' => $score['result_buy'],
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Scores processed successfully!',
        ]);
    }

    public function deleteScore(int $id): JsonResponse
    {
        $score = StageScore::findOrFail($id);
        $score->delete();

        return response()->json([
            'message' => 'Score deleted successfully!',
        ]);
    }

    public function endBattle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bonus_battle_id' => 'required|exists:bonus_battles,id',
        ]);

        $bonusBattle = BonusBattle::findOrFail($validated['bonus_battle_id']);

        $bracket = $bonusBattle->lastActiveStage->activeBracket;
        $this->endBracket($bracket);

        $bonusBattle->stages()->update(['active' => false]);
        $bonusBattle->update(['active' => false]);

        return response()->json([
            'message' => 'Battle ended successfully!',
            'winner' => $this->getTheWinner($bonusBattle),
        ]);
    }

    private function getTheWinner($bonusBattle) {
        $lastEndedStage = $bonusBattle->lastEndedStage;
        $lastBracket = $lastEndedStage?->brackets()
            ->where('is_finished', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastBracket?->winner;
    }

}
