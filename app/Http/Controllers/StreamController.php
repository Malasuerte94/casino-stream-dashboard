<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
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
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $userId = $id;
        $user = User::findOrFail($userId);

        $latestStream = $user->streams()->latest()->first();

        return response()->json([
            'stream' => $latestStream,
        ]);
    }


}
