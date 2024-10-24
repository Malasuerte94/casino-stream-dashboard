<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getYoutubeData(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'url' => 'required|url'
            ]);

            $url = $validated['url'];
            $channelId = $this->extractChannelId($url);

            if (!$channelId) {
                throw new \Exception('Invalid YouTube channel URL');
            }

            $videoId = $this->getLiveVideoId($channelId);

            if (!$videoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No live stream found for this channel',
                ], 404);
            }

            $videoDetails = $this->getVideoDetails($videoId);

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'views' => $videoDetails['viewCount'] ?? null,
                    'likes' => $videoDetails['likeCount'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch YouTube data',
                'error' => $e->getMessage()
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
        return $data['items'][0]['id']['videoId'] ?? null;
    }

    private function getVideoDetails(string $videoId): array
    {
        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
            'part' => 'statistics',
            'id' => $videoId,
            'key' => config('services.youtube.yt_data_view'),
        ]);

        $data = $response->json();
        return $data['items'][0]['statistics'] ?? [];
    }

}
