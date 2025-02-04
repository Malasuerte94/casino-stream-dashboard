<?php

namespace App\Http\Controllers;

use App\Models\LiveStat;
use App\Models\Stream;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StreamController extends Controller
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

        if(!$latestStream) {
            $latestStream = $user->streams()->create();
        }
        return response()->json([
            'stream' => $latestStream,
        ]);
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
        $streamData = [];

        $stream = $user->streams()->create($streamData);
        return response()->json([
            'stream' => $stream,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        $stream = Stream::findOrFail($request->id);
        $stream->casino = $request->casino;
        $stream->deposit = $request->deposit;
        $stream->save();
        return response()->json([
            'stream' => $stream,
        ]);
    }


    /**
     * Show from link
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $latestStream = $user->streams()->latest()->first();

        return response()->json([
            'stream' => $latestStream,
        ]);
    }


    /**
     * @param $userId
     * @return JsonResponse
     */
    public function getYoutubeLink($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $latestStream = $user->streamAccounts()->where('type', 'youtube')->latest()->first();
        return response()->json([
            'youtube' => $latestStream,
        ]);
    }

    /**
     * Fetch YouTube data and update live stats.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getYoutubeData(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'url' => 'required|url',
                'user_id' => 'required|integer',
            ]);

            $url = $validated['url'];
            $userId = $validated['user_id'];

            $user = User::findOrFail($userId);
            $channelId = $this->extractChannelId($url);

            if (!$channelId) {
                throw new \Exception('Invalid YouTube channel URL');
            }

            $cacheKey = "youtube_live_video_id_{$channelId}";
            $videoId = Cache::get($cacheKey);
            $videoDetails = $videoId ? $this->getVideoDetails($videoId) : null;

            if (!$videoDetails) {
                $videoId = $this->getLiveVideoId($channelId);
                if (!$videoId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No live stream found for this channel or quota exceeded',
                    ], 404);
                }
                Cache::put($cacheKey, $videoId, now()->addMinutes(25));
                $videoDetails = $this->getVideoDetails($videoId);
            }

            // Extract data from video details
            $currentViewers = $videoDetails['concurrentViewers'] ?? '0';
            $totalViews = $videoDetails['viewCount'] ?? '0';
            $likes = $videoDetails['likeCount'] ?? '0';

            $liveStat = LiveStat::firstOrNew(['live_id' => $videoId, 'user_id' => $user->id]);

            if (!$liveStat->exists) {
                $liveStat->max_views = 0;
                $liveStat->max_likes = 0;
            }

            $recentStats = LiveStat::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get();

            $recordViews = false;
            $recordLikes = false;

            if ($recentStats->count() >= 10) {
                $maxViewsFromRecent = $recentStats->max('max_views');
                $maxLikesFromRecent = $recentStats->max('max_likes');

                if ($currentViewers > $maxViewsFromRecent) {
                    $liveStat->max_views = $currentViewers;
                    $recordViews = true;
                }
                if ($likes > $maxLikesFromRecent) {
                    $liveStat->max_likes = $likes;
                    $recordLikes = true;
                }
            } else {
                if ($currentViewers > $liveStat->max_views) {
                    $liveStat->max_views = $currentViewers;
                }
                if ($likes > $liveStat->max_likes) {
                    $liveStat->max_likes = $likes;
                }
            }

            $liveStat->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'views' => $currentViewers === '0' ? $totalViews : $currentViewers,
                    'likes' => $likes,
                    'record_views' => $recordViews,
                    'record_likes' => $recordLikes,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch YouTube data',
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    private function extractChannelId(string $url): ?string
    {
        if (preg_match('/channel\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * @param string $channelId
     *
     * @return string|null
     * @throws Exception
     */
    private function getLiveVideoId(string $channelId): ?string
    {
        $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'key' => config('services.youtube.yt_data_view'),
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \Exception($data['error']['message'] ?? 'Unknown error occurred while fetching live video ID');
        }

        return $data['items'][0]['id']['videoId'] ?? null;
    }

    /**
     * @param string $videoId
     * @return array
     * @throws Exception
     */
    private function getVideoDetails(string $videoId): array
    {
        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
            'part' => 'statistics,liveStreamingDetails',
            'id' => $videoId,
            'key' => config('services.youtube.yt_data_view'),
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \Exception($data['error']['message'] ?? 'Unknown error occurred while fetching live details');
        }

        // Combine statistics and live streaming details if available
        $statistics = $data['items'][0]['statistics'] ?? [];
        $liveDetails = $data['items'][0]['liveStreamingDetails'] ?? [];

        // Return a merged array containing both statistics and live details
        return array_merge($statistics, $liveDetails);
    }


}
