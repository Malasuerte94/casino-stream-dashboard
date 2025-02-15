<?php

namespace App\Http\Controllers;

use App\Models\BonusBattle;
use App\Models\BonusBuy;
use App\Models\BonusHunt;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Schedule;

class DiscordController extends Controller
{
    /**
     * Send a message to Discord.
     *
     * @param string $message The message content.
     * @param string|null $webhookUrl Url
     * @param string|null $username Optional username override for the bot.
     * @param string|null $avatarUrl Optional avatar override for the bot.
     * @return void
     */
    public function sendMessage(string $message, string $webhookUrl = null, string $username = null, string $avatarUrl = null): void
    {
        $payload = [
            'content' => $message,
        ];

        if ($username) {
            $payload['username'] = $username;
        }

        if ($avatarUrl) {
            $payload['avatar_url'] = $avatarUrl;
        }

        try {
            $chunks = [];
            $maxLength = 2000;
            $isCodeBlock = false;
            $splitMessage = $message;

            while (strlen($splitMessage) > $maxLength) {
                $splitPos = strrpos(substr($splitMessage, 0, $maxLength), "\n");

                if ($splitPos === false) {
                    $splitPos = $maxLength; // If no newline found, split at max length
                }

                $chunk = substr($splitMessage, 0, $splitPos);
                $splitMessage = substr($splitMessage, $splitPos);

                // Detect if we are inside a code block
                $codeBlockOpenings = substr_count($chunk, "```");
                if ($codeBlockOpenings % 2 !== 0) {
                    $isCodeBlock = !$isCodeBlock;
                    $chunk .= "\n```";  // Close the unclosed code block
                    $splitMessage = "```\n" . $splitMessage; // Reopen for next part
                }

                $chunks[] = $chunk;
            }

            // Add the remaining message part
            if (!empty($splitMessage)) {
                $chunks[] = $splitMessage;
            }

            // Send each chunk separately
            foreach ($chunks as $chunk) {
                $payload['content'] = $chunk;
                Http::post($webhookUrl, $payload);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send message to Discord: ' . $e->getMessage());
        }
    }


    /**
     * Build a detailed schedule message in Romanian and send it to Discord.
     *
     * @param int $scheduleId
     * @return void
     */
    public function sendScheduleMessage(int $scheduleId): void
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $webhookUrl = $user->webhookSchedule;

        // Fetch the schedule with its days
        $schedule = Schedule::with('days')->find($scheduleId);

        if (!$schedule) {
            logger()->error("Schedule with ID {$scheduleId} not found.");
            return;
        }

        // Build the message
        $message = "**Programul de săptămâna asta!**\n\n";
        $message .= "Detalii program @everyone:\n";

        foreach ($schedule->days as $day) {
            $dayName = $this->getRomanianDayName($day->date);
            $message .= "- **{$dayName}** ({$day->date}): **{$day->info}**\n";
        }

        $message .= "\n:calendar: Mulțumesc și ne vedem pe live!";

        $this->sendMessage($message, $webhookUrl);
    }

    /**
     * Build a detailed schedule message in Romanian and send it to Discord.
     *
     * @param BonusBattle $bonusBattle
     * @return void
     */
    public function sendBattleMessage(BonusBattle $bonusBattle): void
    {
        $user = $bonusBattle->user;
        $webhookUrl = $user->webhookHuntBuyBattle;

        $message = $this->buildBonusBattleDiscordMessage($bonusBattle);

        $this->sendMessage($message, $webhookUrl);
    }


    /**
     * Build a detailed Discord message for the Bonus Battle with enhanced display.
     *
     * @param BonusBattle $bonusBattle
     * @return string
     */
    private function buildBonusBattleDiscordMessage(BonusBattle $bonusBattle): string
    {
        $totalCost = 0;
        $totalResult = 0;

        // Header
        $message = ":trophy: **Am terminat un Bonus Battle @everyone! #{$bonusBattle->id}** :trophy:\n";
        $message .= ":money_with_wings: **Titlu**: {$bonusBattle->title} | **Miza**: {$bonusBattle->stake}\n\n";

        // Iterate through each stage
        foreach ($bonusBattle->stages as $stage) {
            $message .= ":crossed_swords: **{$stage->name}**\n";

            foreach ($stage->brackets as $bracket) {
                $participantA = $bracket->participantA->game->name ?? 'N/A';
                $participantB = $bracket->participantB->game->name ?? 'N/A';
                $winner = $bracket->winner->game->name ?? 'N/A';

                // Bracket Header
                $message .= ":medal: `[Bracket #{$bracket->id} | Winner: {$winner}]`\n";
                $message .= "`{$participantA}` :vs: `{$participantB}`\n";

                // Scores Table
                $message .= "```\n";
                $message .= "Participant   | Cost (LEI) | Result (LEI) | Score\n";
                $message .= "-------------|------------|--------------|-------\n";

                // Display scores for participant A
                foreach ($bracket->scores->where('bonus_concurrent_id', $bracket->participant_a_id) as $score) {
                    $isWinner = $score->bonus_concurrent_id === $bracket->winner_id ? '*' : '';
                    $message .= sprintf(
                        "%-13s | %-10.2f | %-12.2f | %-5.2f %s\n",
                        $participantA,
                        $score->cost_buy,
                        $score->result_buy,
                        $score->score,
                        $isWinner
                    );
                    $totalCost += $score->cost_buy;
                    $totalResult += $score->result_buy;
                }

                // Display scores for participant B
                foreach ($bracket->scores->where('bonus_concurrent_id', $bracket->participant_b_id) as $score) {
                    $isWinner = $score->bonus_concurrent_id === $bracket->winner_id ? '*' : '';
                    $message .= sprintf(
                        "%-13s | %-10.2f | %-12.2f | %-5.2f %s\n",
                        $participantB,
                        $score->cost_buy,
                        $score->result_buy,
                        $score->score,
                        $isWinner
                    );
                    $totalCost += $score->cost_buy;
                    $totalResult += $score->result_buy;
                }

                $message .= "```\n";
            }
        }

        // Calculate Profit
        $profit = $totalResult - $totalCost;
        $profitText = $profit >= 0 ? "+{$profit}" : "{$profit}";

        // Summary
        $message .= ":moneybag: **Rezumat**\n";
        $message .= ":arrow_right: **Cost Total**: `{$totalCost} LEI`\n";
        $message .= ":arrow_right: **Rezultat Total**: `{$totalResult} LEI`\n";
        $message .= ":arrow_right: **Profit**: `{$profitText} LEI`\n\n";
        $message .= ":trophy: **Joc Câștigător**: {$winner}\n\n";

        // Footer
        $message .= ":calendar: **Mulțumesc și hai pe live!**";

        return $message;
    }


    /**
     * Build a detailed schedule message in Romanian and send it to Discord.
     *
     * @param BonusBuy|BonusHunt $bonusList
     * @return void
     */
    public function sendHuntBuyMessage(BonusBuy|BonusHunt $bonusList): void
    {
        $user = $bonusList->user;
        $webhook = $user->webhookHuntBuyBattle;

        if ($bonusList instanceof BonusBuy) {
            $message = $this->buildBonusBuyDiscordMessage($bonusList);
        } elseif ($bonusList instanceof BonusHunt) {
            $message = $this->buildBonusHuntDiscordMessage($bonusList);
        } else {
            return;
        }
        $this->sendMessage($message, $webhook);
    }


    /**
     * Build a detailed Discord message for the Bonus Buy.
     *
     * @param BonusBuy $bonusBuy
     * @return string
     */
    private function buildBonusBuyDiscordMessage(BonusBuy $bonusBuy): string
    {
        $bonusBuyGames = $bonusBuy->bonusBuyGames;

        // Extract necessary statistics
        $totalPrice = $bonusBuyGames->sum('price'); // Total cost of all bonus buys
        $totalResult = $bonusBuyGames->sum('result'); // Total result of all bonus buys
        $profit = $totalResult - $totalPrice; // Profit/Loss
        $profitText = $profit >= 0 ? "+{$profit}" : "{$profit}";

        $topMultiplier = $bonusBuyGames->max('multiplier'); // Highest multiplier
        $topResult = $bonusBuyGames->max('result'); // Highest result
        $averageMultiplier = $bonusBuyGames->avg('multiplier'); // Average multiplier
        $topMultiplierGame = $bonusBuyGames->where('multiplier', $topMultiplier)->first();
        $topResultGame = $bonusBuyGames->where('result', $topResult)->first();
        $worstGame = $bonusBuyGames->where('multiplier', $bonusBuyGames->min('multiplier'))->first();

        // Header
        $message = ":money_with_wings: **Am terminat un Bonus Buy @everyone! #{$bonusBuy->id}** :money_with_wings:\n\n";

        // Summary Row
        $message .= sprintf(
            ":chart_with_upwards_trend: **COST**: `%.2f LEI` | **Rezultat**: `%.2f LEI` | **Profit**: `%s LEI`\n\n",
            $totalPrice,
            $totalResult,
            $profitText
        );

        // Summary Stats
        $message .= ":star2: **Rezumat:**\n";
        $message .= sprintf(
            ":arrow_up: **Top X**: `%.2f`\n:moneybag: **Top Plata**: `%.2f LEI`\n:chart_with_upwards_trend: **Avg X**: `%.2f`\n",
            $topMultiplier,
            $topResult,
            $averageMultiplier
        );
        $message .= sprintf(
            ":sparkles: **Top Joc**: `%s`\n:money_with_wings: **Top Plata**: `%s`\n:skull: **Cel mai rău**: `%s`\n\n",
            $topMultiplierGame ? $topMultiplierGame->game->name : 'N/A',
            $topResultGame ? $topResultGame->game->name : 'N/A',
            $worstGame ? $worstGame->game->name : 'N/A'
        );

        // Table Header
        $message .= ":game_die: **Mai jos tabel cu toate jocurile:**\n";
        $message .= "`| Nr | Nume Joc              | Miza (LEI) | Preț (LEI) | X         | Plata (LEI) |`\n";
        $message .= "`|----|-----------------------|------------|------------|-----------|-------------|`\n";

        // Table Rows
        foreach ($bonusBuyGames as $index => $game) {
            $message .= sprintf(
                "`| %-2d | %-21s | %-10.2f | %-10.2f | %-9.2f | %-11.2f |`\n",
                $index + 1,
                substr($game->game->name, 0, 21), // Truncate name to 21 characters for alignment
                $game->stake,
                $game->price,
                $game->multiplier,
                $game->result
            );
        }

        // Footer
        $message .= "\n:calendar: **Mulțumesc și hai pe live!**";

        return $message;
    }


    private function buildBonusHuntDiscordMessage($bonusList): string
    {
        $bonusHuntGames = $bonusList->bonusHuntGames;

        // Extract necessary statistics
        $topMultiplier = $bonusHuntGames->max('multiplier');
        $topResult = $bonusHuntGames->max('result');
        $averageMultiplier = $bonusHuntGames->avg('multiplier');
        $topMultiplierGame = $bonusHuntGames->where('multiplier', $topMultiplier)->first();
        $topResultGame = $bonusHuntGames->where('result', $topResult)->first();
        $worstGame = $bonusHuntGames->where('multiplier', $bonusHuntGames->min('multiplier'))->first();

        // Header
        $message = ":trophy: **Am terminat un bonus hunt @everyone! #{$bonusList->id}** :trophy:\n\n";

        $cost = $bonusList->start ?? 0; // If `start` is null, default to 0
        $result = $bonusList->result ?? 0; // If `result` is null, default to 0
        $profit = $result - $cost;
        $profitText = $profit >= 0 ? "+{$profit}" : "{$profit}";

        $message .= sprintf(
            ":money_with_wings: **COST HUNT**: `%.2f LEI` | **Rezultat**: `%.2f LEI` | **Profit**: `%s LEI`\n\n",
            $cost,
            $result,
            $profitText
        );

        // Summary
        $message .= ":star2: **Rezumat:**\n";
        $message .= sprintf(
            ":arrow_up: **Top X**: `%.2f`\n:moneybag: **Top Plata**: `%.2f LEI`\n:chart_with_upwards_trend: **Avg X**: `%.2f`\n",
            $topMultiplier,
            $topResult,
            $averageMultiplier
        );
        $message .= sprintf(
            ":sparkles: **Top Joc**: `%s`\n:money_with_wings: **Top Plata**: `%s`\n:skull: **Cel mai rău**: `%s`\n\n",
            $topMultiplierGame ? $topMultiplierGame->game->name : 'N/A',
            $topResultGame ? $topResultGame->game->name : 'N/A',
            $worstGame ? $worstGame->game->name : 'N/A'
        );

        // Table Header
        $message .= ":game_die: **Mai jos tabel cu toate jocurile:**\n";
        $message .= "`| Nr | Nume Joc              | Miza (LEI) | X         | Plata (LEI) |`\n";
        $message .= "`|----|-----------------------|------------|-----------|-------------|`\n";

        // Table Rows
        foreach ($bonusHuntGames as $index => $game) {
            $message .= sprintf(
                "`| %-2d | %-21s | %-10.2f | %-9.2f | %-11.2f |`\n",
                $index + 1,
                substr($game->game->name, 0, 21), // Truncate name to 21 characters for alignment
                $game->stake,
                $game->multiplier,
                $game->result
            );
        }

        // Footer
        $message .= "\n:calendar: **Mulțumesc și hai pe live!**";

        return $message;
    }

    /**
     * Get the Romanian day name for a given date.
     *
     * @param string $date
     * @return string
     */
    public function getRomanianDayName(string $date): string
    {
        $days = [
            'Duminică',
            'Luni',
            'Marți',
            'Miercuri',
            'Joi',
            'Vineri',
            'Sâmbătă',
        ];
        $dayIndex = date('w', strtotime($date)); // Get day index (0 for Sunday, 6 for Saturday)
        return $days[$dayIndex];
    }
}