<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Schedule;
use App\Models\UserSetting;

class DiscordController extends Controller
{
    /**
     * Send a message to Discord.
     *
     * @param string $message The message content.
     * @param User $user The request containing user info.
     * @param string|null $username Optional username override for the bot.
     * @param string|null $avatarUrl Optional avatar override for the bot.
     * @return void
     */
    public function sendMessage(string $message, User $user, string $username = null, string $avatarUrl = null)
    {
        $webhookUrl = $this->getUserWebhook($user);

        if (!$webhookUrl) {
            logger()->error('Discord webhook URL is missing for user ID: ' . $user->id);
            return;
        }

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
            Http::post($webhookUrl, $payload);
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

        $this->sendMessage($message, $user);
    }

    /**
     * Get the Discord webhook URL for a user.
     *
     * @param User $user
     * @return string|null
     */
    private function getUserWebhook($user): ?string
    {
        $setting = UserSetting::where('user_id', $user->id)
            ->where('name', 'discord_wbh_schedule')
            ->first();

        return $setting?->value;
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