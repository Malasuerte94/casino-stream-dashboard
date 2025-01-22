<?php
namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleDay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // Get the latest schedule
    public function getLatest()
    {
        $schedule = Schedule::with('days')->latest()->first();
        return response()->json($schedule);
    }

    // Get all schedules
    public function getAll()
    {
        $schedules = Schedule::with('days')->orderByDesc('id')->get();
        return response()->json($schedules);
    }

    public function getWeeklySchedule($id)
    {
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        // Fetch schedules for the given user ID and current week
        $schedules = Schedule::with(['days' => function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
        }])
            ->where('user_id', $id)
            ->whereHas('days', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            })->get();

        return response()->json($schedules);
    }

    // Get a specific schedule
    public function getSchedule(Schedule $schedule)
    {
        return response()->json($schedule->load('days'));
    }

    public function toggleStatus(ScheduleDay $schedule_day)
    {
        $schedule_day->update(['active' => !$schedule_day->active]);

        if(!$schedule_day->active){
            $user = $schedule_day->schedule->user;
            $webhookUrl = $user->webhookSchedule;
            $discord = new DiscordController();
            $dataString = $discord->getRomanianDayName($schedule_day->date);
            $message = "Atenție @everyone! Ziua de **" . $dataString. "** ( " . $schedule_day->date . ") a fost **ANULATĂ**";
            $discord->sendMessage($message, $webhookUrl);
        }

        return response()->json(['message' => 'Status updated successfully.', 'day' => $schedule_day]);
    }

    public function editDay(ScheduleDay $schedule_day, Request $request)
    {
        $schedule_day->update($request->only('info'));
        return response()->json(['message' => 'Day updated successfully.', 'day' => $schedule_day]);
    }

    // Create a new schedule
    public function createSchedule(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'days' => 'required|array',
            'days.*.info' => 'required|string',
            'days.*.date' => 'required|date',
            'announceToDiscord' => 'boolean',
        ]);

        $schedule = Schedule::create(['user_id' => $user->id]);

        foreach ($validated['days'] as $day) {
            $schedule->days()->create($day);
        }

        if ($validated['announceToDiscord'] ?? true) {
            $discord = new DiscordController();
            $discord->sendScheduleMessage($schedule->id);
        }

        return response()->json(['message' => 'Schedule created successfully.', 'schedule' => $schedule->load('days')]);
    }
}
