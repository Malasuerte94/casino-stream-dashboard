<?php

namespace App\Http\Controllers;

use App\Models\BattlePrediction;
use App\Models\BattlePredictionWinner;
use App\Models\BonusBattle;
use App\Models\BonusBuy;
use App\Models\BonusConcurrent;
use App\Models\BonusHunt;
use App\Models\Bracket;
use App\Models\StageScore;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BonusBattleController extends Controller
{

    public function test()
    {
        //        $user = auth()->user();
        //
        //        if (!$user || !$user->streamlabs_access_token) {
        //            return response()->json(['error' => 'Streamlabs token not found.'], 403);
        //        }
        //        $client = new \GuzzleHttp\Client();
        //        try {
        //            $response = $client->request('GET', 'https://streamlabs.com/api/v2.0/points', [
        //                'headers' => [
        //                    'Authorization' => 'Bearer ' . $user->streamlabs_access_token,
        //                    'Accept'        => 'application/json',
        //                ],
        //            ]);
        //            $message = json_decode($response->getBody(), true);
        //            return response()->json($message);
        //        } catch (\Exception $e) {
        //            // In case of an error, return an error response.
        //            return response()->json(['error' => 'Unable to get points: ' . $e->getMessage()], 500);
        //        }
    }


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


        //history
        $stageBrackets = $activeBattle->stages()
            ->with([
                'brackets' => function ($query) {
                    $query->where('is_finished', true)->orderBy('updated_at', 'desc');
                }
            ])
            ->get()
            ->flatMap(function ($stage) {
                return $stage->brackets->map(function ($bracket) {
                    return [
                        'id' => $bracket->id,
                        'bonus_stage_id' => $bracket->bonus_stage_id,
                        'participant_a' => $bracket->participantA?->game->name ?? 'N/A',
                        'participant_a_score' => $bracket->scores()
                            ->where('bonus_concurrent_id', $bracket->participantA->id)
                            ->sum('score') ?: '0',
                        'participant_b' => $bracket->participantB?->game->name ?? 'N/A',
                        'participant_b_score' => $bracket->scores()
                            ->where('bonus_concurrent_id', $bracket->participantB->id)
                            ->sum('score') ?: '0',
                        'is_finished' => $bracket->is_finished,
                        'winner' => $bracket->winner?->game->name ?? 'N/A',
                    ];
                });
            });

        $allBattleBrackets = $activeBattle->stages()
            ->with([
                'brackets' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                }
            ])
            ->get()
            ->flatMap(fn($stage) => $stage->brackets);

        $allConcurrents = $activeBattle->concurrents()->get()->map(function ($concurrent) use ($allBattleBrackets) {
            $isEliminated = $allBattleBrackets->contains(function ($bracket) use ($concurrent) {
                return $bracket->is_finished && $bracket->winner_id !== $concurrent->id &&
                    ($bracket->participant_a_id === $concurrent->id || $bracket->participant_b_id === $concurrent->id);
            });
            $concurrent->load('game');
            return array_merge($concurrent->toArray(), ['is_eliminated' => $isEliminated]);
        });
        //history

        $infoBattle = $this->getCurentBattleDetails($activeBattle);
        $infoBattle['total_battles'] = $totalBattlesCount;
        $infoBattle['history']['concurrents'] = $allConcurrents;
        $infoBattle['history']['stage_brackets'] = $stageBrackets;

        return response()->json($infoBattle);
    }


    /**
     * Get PUBLIC BATTLE INFO
     * @param $id
     * @return JsonResponse
     */
    public function getBonusBattleInfo($id): JsonResponse
    {
        $user = User::findOrFail($id);

        $activeBattle = $user->bonusBattles()->where('active', true)->first();

        if (!$activeBattle) {
            $activeBattle = $user->bonusBattles()
                ->latest('created_at')
                ->first();
        }

        $battleInfo = $this->getBonusBattleInfoForBattle($activeBattle);
        return response()->json($battleInfo);
    }

    /**
     * Get PUBLIC BATTLEs INFO
     * @param $id
     * @return JsonResponse
     */
    public function getBonusBattleHistory($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $battles = [];
        $allBattles = $user->bonusBattles()->orderBy('created_at', 'desc')->get();

        foreach ($allBattles as $battle) {
            $battleInfo = $this->getBonusBattleInfoForBattle($battle);
            $battles[] = $battleInfo;
        }
        return response()->json(['bonusBattles' => $battles]);
    }

    /**
     * Get PUBLIC BATTLE INFO
     * @param $id
     * @return JsonResponse
     */
    public function getSingleBonusBattleInfo($id): JsonResponse
    {
        $bonusBattle = BonusBattle::findOrFail($id);
        $battleInfo = $this->getFullBonusBattleInfoForBattle($bonusBattle);
        return response()->json($battleInfo);
    }


    /**
     * @param $activeBattle
     * @return array
     */
    private function getFullBonusBattleInfoForBattle($activeBattle): array
    {
        $result = [
            'bonusBattle' => [
                'battle' => $activeBattle,
                'stages' => []
            ]
        ];

        $totalCost = 0;
        $totalResult = 0;

        foreach ($activeBattle->stages as $stage) {
            $stageData = [
                'id' => $stage->id,
                'name' => $stage->name,
                'brackets' => []
            ];

            foreach ($stage->brackets as $bracket) {
                $bracketData = [
                    'id' => $bracket->id,
                    'participantA' => [
                        'id' => $bracket->participant_a_id,
                        'game' => $bracket->participantA->game->name ?? 'N/A',
                        'full_game' => $bracket->participantA->game
                    ],
                    'participantB' => [
                        'id' => $bracket->participant_b_id,
                        'game' => $bracket->participantB->game->name ?? 'N/A',
                        'full_game' => $bracket->participantB->game
                    ],
                    'winner' => [
                        'id' => $bracket->winner_id,
                        'game' => $bracket->winner->game->name ?? 'N/A'
                    ],
                    'scores' => []
                ];

                foreach ($bracket->scores as $score) {
                    $bracketData['scores'][] = [
                        'participant_id' => $score->bonus_concurrent_id,
                        'cost' => $score->cost_buy,
                        'result' => $score->result_buy,
                        'score' => $score->score,
                        'is_winner' => $score->bonus_concurrent_id === $bracket->winner_id
                    ];

                    $totalCost += $score->cost_buy;
                    $totalResult += $score->result_buy;
                }

                $stageData['brackets'][] = $bracketData;
            }

            $result['bonusBattle']['stages'][] = $stageData;
        }

        $result['bonusBattle']['summary'] = [
            'total_cost' => $totalCost,
            'total_result' => $totalResult,
            'profit' => $totalResult - $totalCost,
            'winning_game' => $result['bonusBattle']['stages'][0]['brackets'][0]['winner']['game'] ?? null
        ];

        $allBattleBrackets = $activeBattle->stages->flatMap->brackets;
        $result['bonusBattle']['participants'] =
            $activeBattle->concurrents()->get()->map(function ($concurrent) use ($allBattleBrackets) {
                $isEliminated = $allBattleBrackets->contains(function ($bracket) use ($concurrent) {
                    return $bracket->is_finished && $bracket->winner_id !== $concurrent->id &&
                        ($bracket->participant_a_id === $concurrent->id ||
                            $bracket->participant_b_id === $concurrent->id);
                });
                $concurrent->load('game');
                return array_merge($concurrent->toArray(), ['is_eliminated' => $isEliminated]);
            })->toArray();

        return $result;
    }


    /**
     * @param $activeBattle
     * @return array
     */
    private function getBonusBattleInfoForBattle($activeBattle): array
    {
        $activeInfo = $this->getCurentBattleDetails($activeBattle);
        $activeStage = $activeBattle->lastActiveStage ?? $activeBattle->lastEndedStage;

        $stageBrackets =
            $activeStage?->brackets()->where('is_finished', true)->orderBy('updated_at', 'desc')->get()->map(
                function ($bracket) {
                    return [
                        'id' => $bracket->id,
                        'bonus_stage_id' => $bracket->bonus_stage_id,
                        'participant_a' => $bracket->participantA?->game->name ?? 'N/A',
                        'participant_a_score' => $bracket->scores()
                            ->where('bonus_concurrent_id', $bracket->participantA->id)
                            ->sum('score') ?: '0',
                        'participant_b' => $bracket->participantB?->game->name ?? 'N/A',
                        'participant_b_score' => $bracket->scores()
                            ->where('bonus_concurrent_id', $bracket->participantB->id)
                            ->sum('score') ?: '0',
                        'is_finished' => $bracket->is_finished,
                        'winner' => $bracket->winner?->game->name ?? 'N/A',
                    ];
                }
            );

        $allBattleBrackets = $activeBattle->stages()
            ->with([
                'brackets' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                }
            ])
            ->get()
            ->flatMap(function ($stage) {
                return $stage->brackets;
            });

        $allConcurrents = $activeBattle->concurrents()->get()->map(function ($concurrent) use ($allBattleBrackets) {
            $isEliminated = $allBattleBrackets->contains(function ($bracket) use ($concurrent) {
                return $bracket->is_finished && $bracket->winner_id !== $concurrent->id &&
                    ($bracket->participant_a_id === $concurrent->id || $bracket->participant_b_id === $concurrent->id);
            });
            $concurrent->load('game');
            return array_merge($concurrent->toArray(), ['is_eliminated' => $isEliminated]);
        });

        $remainingConcurrents = $allConcurrents->filter(function ($concurrent) {
            return !$concurrent['is_eliminated'];
        });

        $battleWinner = null;

        if ($remainingConcurrents->count() === 1) {
            $battleWinner = $remainingConcurrents->first();
        }

        $allScores = $allBattleBrackets->flatMap(function ($bracket) {
            return $bracket->scores;
        });
        $bestScore = $allScores->max('score');
        $avgScore = $allScores->avg('score');
        $totalProfit = $allScores->sum(function ($score) {
            return $score->result_buy - $score->cost_buy;
        });
        $totalCost = $allScores->sum(function ($score) {
            return $score->cost_buy;
        });

        $activeInfo['stage_brackets'] = $stageBrackets;
        $activeInfo['all_concurrents'] = $allConcurrents;
        $activeInfo['current_score'] = $activeStage->activeBracket?->scores()->get();
        $activeInfo['battle_winner'] = $battleWinner;
        $activeInfo['best_score'] = $bestScore;
        $activeInfo['avg_score'] = round($avgScore, 2);
        $activeInfo['total_profit'] = $totalProfit;
        $activeInfo['total_cost'] = $totalCost;

        return $activeInfo;
    }

    private function getCurentBattleDetails($activeBattle): array
    {
        $activeStage = $activeBattle->lastActiveStage ?? $activeBattle->lastEndedStage;

        $activeStageScores = $activeStage->activeBracket?->scores()->get();

        if (!$activeStageScores) {
            $activeStageScores = $activeStage->latestBracket->scores()->get();
        }

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

        $participantA = $nextBracket->participantA->load('game');
        $participantB = $nextBracket->participantB->load('game');

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
            'prize' => 'string|nullable',
            'buys' => 'int|nullable',
            'isRandom' => 'boolean',
            'concurrents' => 'required|array|min:2',
            'concurrents.*.game_id' => 'required|int',
            'concurrents.*.for_user' => 'string|max:255|nullable',
        ]);

        $bonusBattle = BonusBattle::create([
            'title' => $validated['title'],
            'stake' => $validated['stake'] ?? 'Aleatorie',
            'prize' => $validated['prize'] ?? null,
            'buys' => $validated['buys'] ?? 1,
            'active' => true,
            'user_id' => $user->id,
        ]);

        foreach ($validated['concurrents'] as $concurrent) {
            $bonusBattle->concurrents()->create([
                'name' => 'CUSTOM',
                'game_id' => $concurrent['game_id'],
                'for_user' => $concurrent['for_user']
            ]);
        }

        $bonusBattle->stages()->create(['name' => 'Stage 1']);

        $this->generateFirstBrackets($bonusBattle, $validated['isRandom']);

        return response()->json(['message' => 'Bonus battle created successfully!', 'bonus_battle' => $bonusBattle],
            201);
    }


    /**
     * Generate brackets for a given BonusBattle.
     *
     * @param BonusBattle $bonusBattle
     * @param bool $isRandom
     * @return void
     */
    function generateFirstBrackets(BonusBattle $bonusBattle, bool $isRandom): void
    {
        $concurrents = $bonusBattle->concurrents;

        $activeStage = $bonusBattle->lastActiveStage;

        if (!$activeStage) {
            $activeStage = $bonusBattle->stages()->create(['name' => 'Stage 1']);
        }

        $orderedConcurrents = $isRandom ? $concurrents->shuffle() : $concurrents;

        $pairs = $orderedConcurrents->chunk(2)->map(function ($chunk) {
            return $chunk->values()->all();
        })->toArray();

        foreach ($pairs as $pair) {
            if (empty($pair[0])) {
                continue;
            }
            Bracket::create([
                'bonus_stage_id'    => $activeStage->id,
                'participant_a_id'  => $pair[0]->id,
                'participant_b_id'  => $pair[1]->id ?? null,
                'is_finished'       => false,
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
            $pair = $pair->values();

            Bracket::create([
                'bonus_stage_id' => $newStage->id,
                'participant_a_id' => $pair[0]->id,
                'participant_b_id' => $pair[1]->id,
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
            'bracket.*.scores.*.score' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,5})?$/'
        ]);

        $bracket = Bracket::findOrFail($validated['active_bracket']);
        $bonusBattle = $bracket->stage->bonusBattle;
        $bonusBattle->loadMissing('stages.brackets.scores');

        foreach ($validated['bracket'] as $row) {
            foreach ($row['scores'] as $score) {
                $lastCostBuy = $score['cost_buy'];
                if ($lastCostBuy === 0) {
                    $allScores = $bonusBattle->stages->flatMap(function ($stage) {
                        return $stage->brackets->flatMap(function ($bracketItem) {
                            return $bracketItem->scores;
                        });
                    });
                    $lastScore = $allScores->where('bonus_concurrent_id', $score['bonus_concurrent_id'])
                        ->sortByDesc('id')
                        ->first();
                    if ($lastScore) {
                        $lastCostBuy = $lastScore->cost_buy;
                    }
                }

                if (!empty($score['id'])) {
                    $scoreModel = $bracket->scores()->find($score['id']);
                    if ($scoreModel) {
                        $scoreModel->bonus_concurrent_id = $score['bonus_concurrent_id'];
                        $scoreModel->score = $score['score'];
                        $scoreModel->cost_buy = $lastCostBuy;
                        $scoreModel->result_buy = $score['result_buy'];
                        $scoreModel->save();
                    }
                } else {
                    $bracket->scores()->create([
                        'bonus_concurrent_id' => $score['bonus_concurrent_id'],
                        'score' => $score['score'],
                        'cost_buy' => $lastCostBuy,
                        'result_buy' => $score['result_buy'],
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Scores processed successfully!'
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deleteScore(int $id): JsonResponse
    {
        $score = StageScore::find($id);
        if (!$score) {
            return response()->json(['message' => 'Score not found'], 404);
        }
        $score->delete();
        return response()->json(['message' => 'Score deleted successfully!']);
    }

    public function endBattle(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bonus_battle_id' => 'required|exists:bonus_battles,id',
        ]);

        $bonusBattle = $user->bonusBattles()->findOrFail($validated['bonus_battle_id']);
        $bracket = $bonusBattle->lastActiveStage->activeBracket;
        $this->endBracket($bracket);

        $bonusBattle->stages()->update(['active' => false]);
        $bonusBattle->update(['active' => false, 'is_open' => false]);

        $this->generateWinners($bonusBattle);

        $discord = new DiscordController();
        $discord->sendBattleMessage($bonusBattle);

        return response()->json([
            'message' => 'Battle ended successfully!',
            'winner' => $this->getTheWinner($bonusBattle),
        ]);
    }

    public function endBattleForced(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bonus_battle_id' => 'required|exists:bonus_battles,id',
        ]);

        $bonusBattle = BonusBattle::findOrFail($validated['bonus_battle_id']);
        foreach ($bonusBattle->stages as $stage) {
            $stage->brackets()->update(['is_finished' => true]);
        }
        $bonusBattle->stages()->update(['active' => false]);
        $bonusBattle->update(['active' => false]);

        return response()->json([
            'message' => 'Battle ended successfully!',
        ]);
    }

    public function switchPredictions(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bonus_battle_id' => 'required|exists:bonus_battles,id',
            'is_open' => 'required|boolean',
        ]);
        $bonusBattle = $user->bonusBattles()->findOrFail($validated['bonus_battle_id']);
        $bonusBattle->update(['is_open' => !$bonusBattle->is_open]);

        return response()->json([
            'message' => 'Predictions switched successfully!',
        ]);
    }

    public function editConcurrent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|exists:bonus_concurrents,id',
            'game_id' => 'required|exists:games,id',
        ]);

        $concurrent = BonusConcurrent::findOrFail($validated['id']);
        $concurrent->game_id = $validated['game_id'];
        $concurrent->save();

        return response()->json([
            'message' => 'Concurrent edited successfully!',
        ]);
    }

    private function getTheWinner($bonusBattle)
    {
        $lastEndedStage = $bonusBattle->lastEndedStage;
        $lastBracket = $lastEndedStage?->brackets()
            ->where('is_finished', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastBracket?->winner->load('game');
    }

    /**
     * Generate winners for the bonus battle.
     *
     * This function extracts:
     * 1. The tournament winner (the participant that was not eliminated).
     * 2. The lowest paying game – the participant associated with the lowest score.
     * 3. The highest paying game – the participant associated with the highest score.
     *
     * It expects a BonusBattle instance, which has relationships:
     * - stages (each with brackets and scores)
     * - concurrents() returning a query builder for participants.
     *
     * @param BonusBattle $bonusBattle The bonus battle instance.
     * @return void
     */
    private function generateWinners(BonusBattle $bonusBattle): void
    {
        try {
            $winners = [];

            $allBrackets = $bonusBattle->stages->flatMap(function ($stage) {
                return $stage->brackets;
            });

            $participants = $bonusBattle->concurrents()->get()->map(function ($concurrent) use ($allBrackets) {
                $isEliminated = $allBrackets->contains(function ($bracket) use ($concurrent) {
                    return $bracket->is_finished
                        &&
                        $bracket->winner_id !== $concurrent->id
                        &&
                        ($bracket->participant_a_id === $concurrent->id ||
                            $bracket->participant_b_id === $concurrent->id);
                });
                $concurrent->load('game');
                return array_merge($concurrent->toArray(), ['is_eliminated' => $isEliminated]);
            });

            foreach ($participants as $participant) {
                if (!$participant['is_eliminated']) {
                    $winners['tournament_winner'] = $participant;
                    break;
                }
            }

            $lowestScore = null;
            $highestScore = null;
            $lowestScoreParticipantId = null;
            $highestScoreParticipantId = null;

            foreach ($bonusBattle->stages as $stage) {
                foreach ($stage->brackets as $bracket) {
                    foreach ($bracket->scores as $score) {
                        $scoreValue = $score->score;
                        if (is_null($lowestScore) || $scoreValue < $lowestScore) {
                            $lowestScore = $scoreValue;
                            $lowestScoreParticipantId = $score->bonus_concurrent_id;
                        }
                        if (is_null($highestScore) || $scoreValue > $highestScore) {
                            $highestScore = $scoreValue;
                            $highestScoreParticipantId = $score->bonus_concurrent_id;
                        }
                    }
                }
            }

            $lowestConcurrent = null;
            $highestConcurrent = null;
            foreach ($participants as $participant) {
                if ($participant['id'] == $lowestScoreParticipantId) {
                    $lowestConcurrent = $participant;
                }
                if ($participant['id'] == $highestScoreParticipantId) {
                    $highestConcurrent = $participant;
                }
            }

            $winners['lowest_paying'] = [
                'concurrent' => $lowestConcurrent,
                'score' => $lowestScore,
            ];

            $winners['highest_paying'] = [
                'concurrent' => $highestConcurrent,
                'score' => $highestScore,
            ];

            $candidatesForWinner = BattlePrediction::where('bonus_battle_id', $bonusBattle->id)
                ->where('game_winner_id', $winners['tournament_winner']['id'])
                ->get();

            $candidatesForLowestGame = BattlePrediction::where('bonus_battle_id', $bonusBattle->id)
                ->where('game_lowest_multi_id', $winners['lowest_paying']['concurrent']['id'])
                ->get();

            $candidatesForHighestGame = BattlePrediction::where('bonus_battle_id', $bonusBattle->id)
                ->where('game_biggest_multi_id', $winners['highest_paying']['concurrent']['id'])
                ->get();

            $selectedWinnerCandidate = $candidatesForWinner->isEmpty() ? null : $candidatesForWinner->random();
            $selectedLowestCandidate = $candidatesForLowestGame->isEmpty() ? null : $candidatesForLowestGame->random();
            $selectedHighestCandidate =
            $candidatesForHighestGame->isEmpty() ? null : $candidatesForHighestGame->random();

            BattlePredictionWinner::create([
                'bonus_battle_id' => $bonusBattle->id,
                'game_winner_bp_id' => $selectedWinnerCandidate ? $selectedWinnerCandidate->id : null,
                'game_lowest_bp_id' => $selectedLowestCandidate ? $selectedLowestCandidate->id : null,
                'game_highest_bp_id' => $selectedHighestCandidate ? $selectedHighestCandidate->id : null,
            ]);
        } catch (Exception $e) {
            return;
        }
    }


}
